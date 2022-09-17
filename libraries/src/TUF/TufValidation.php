<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2022 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\TUF;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\TUF\HttpFileFetcher;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\ParameterType;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tuf\Client\GuzzleFileFetcher;
use Tuf\Client\Updater;
use Tuf\Exception\Attack\FreezeAttackException;
use Tuf\Exception\Attack\RollbackAttackException;
use Tuf\Exception\Attack\SignatureThresholdException;
use Tuf\Exception\MetadataException;
use Tuf\JsonNormalizer;

\defined('JPATH_PLATFORM') or die;

/**
 * @since  __DEPLOY_VERSION__
 */
class TufValidation
{
	/**
	 * The id of the extension to be updated
	 *
	 * @var integer
	 */
	private $extensionId;

	/**
	 * The params of the validator
	 *
	 * @var mixed
	 */
	private $params;

	/**
	 * Validating updates with TUF
	 *
	 * @param   integer  $extensionId  The ID of the extension to be checked
	 * @param   mixed    $params       The parameters containing the Base-URI, the Metadata- and Targets-Path and mirrors for the update
	 */
	public function __construct(int $extensionId, $params)
	{
		$this->extensionId = $extensionId;

		$resolver = new OptionsResolver;

		try
		{
			$this->configureTufOptions($resolver);
		}
		catch (\Exception $e)
		{
		}

		try
		{
			$params = $resolver->resolve($params);
		}
		catch (\Exception $e)
		{
			if ($e instanceof UndefinedOptionsException || $e instanceof InvalidOptionsException)
			{
				throw $e;
			}
		}

		$this->params = $params;
	}

	/**
	 * Configures default values or pass arguments to params
	 *
	 * @param   OptionsResolver $resolver  The OptionsResolver for the params
	 * @return void
	 */
	protected function configureTufOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'url_prefix' => 'https://raw.githubusercontent.com',
				'metadata_path' => '/joomla/updates/test/repository/',
				'targets_path' => '/targets/',
				'mirrors' => [],
			]
		)
			->setAllowedTypes('url_prefix', 'string')
			->setAllowedTypes('metadata_path', 'string')
			->setAllowedTypes('targets_path', 'string')
			->setAllowedTypes('mirrors', 'array');
	}

	/**
	 * Checks for updates and writes it into the database if they are valid. Then it gets the targets.json content and
	 * returns it
	 *
	 * @return mixed Returns the targets.json if the validation is successful, otherwise null
	 */
	public function getValidUpdate()
	{
		$db = Factory::getContainer()->get(DatabaseDriver::class);

		$fileFetcher = HttpFileFetcher::createFromUri($this->params['url_prefix'], $this->params['metadata_path'], $this->params['targets_path']);

		$storage = new DatabaseStorage($db, $this->extensionId);

		$updater = new Updater(
			$fileFetcher,
			$this->params['mirrors'],
			$storage
		);

		try
		{
			// Refresh the data if needed, it will be written inside the DB, then we fetch it afterwards and return it to
			// the caller
			$updater->refresh();

			return $storage['targets.json'];
		}
		catch (MetadataException $e)
		{
			$this->rollBack();
			Factory::getApplication()->enqueueMessage(Text::_('JLIB_INSTALLER_TUF_INVALID_METADATA'), 'error');
			return null;
		}
		catch (FreezeAttackException $e)
		{
			$this->rollBack();
			Factory::getApplication()->enqueueMessage(Text::_('JLIB_INSTALLER_TUF_FREEZE_ATTACK'), 'error');
			return null;
		}
		catch (RollbackAttackException $e)
		{
			$this->rollBack();
			Factory::getApplication()->enqueueMessage(Text::_('JLIB_INSTALLER_TUF_ROLLBACK_ATTACK'), 'error');
			return null;
		}
		catch (SignatureThresholdException $e)
		{
			$this->rollBack();
			Factory::getApplication()->enqueueMessage(Text::_('JLIB_INSTALLER_TUF_SIGNATURE_THRESHOLD'), 'error');
			return null;
		}
	}

	/**
	 * When the validation fails, for example when one file is written but the others don't, we roll back everything
	 *
	 * @return void
	 */
	private function rollBack() {
		$db = Factory::getContainer()->get(DatabaseDriver::class);
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__tuf_metadata'))
			->columns(['snapshot_json', 'targets_json', 'timestamp_json']);
		$db->setQuery($query);
	}
}
