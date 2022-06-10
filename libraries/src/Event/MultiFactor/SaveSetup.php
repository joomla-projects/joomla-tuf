<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2022 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Event\MultiFactor;

\defined('JPATH_PLATFORM') or die;

use DomainException;
use Joomla\CMS\Event\AbstractImmutableEvent;
use Joomla\CMS\Event\Result\ResultAware;
use Joomla\CMS\Event\Result\ResultAwareInterface;
use Joomla\CMS\Event\Result\ResultTypeArrayAware;
use Joomla\Component\Users\Administrator\Table\MfaTable;
use Joomla\Input\Input;

/**
 * Concrete Event class for the onUserMultifactorSaveSetup event
 *
 * @since __DEPLOY_VERSION__
 */
class SaveSetup extends AbstractImmutableEvent implements ResultAwareInterface
{
	use ResultAware;
	use ResultTypeArrayAware;

	/**
	 * Public constructor
	 *
	 * @param   MfaTable  $record  The record to save into
	 * @param   Input     $input   The application input object
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(MfaTable $record, Input $input)
	{
		parent::__construct(
			'onUserMultifactorSaveSetup',
			[
				'record' => $record,
				'input'  => $input,
			]
		);

		$this->resultIsNullable = true;
	}

	/**
	 * Validate the value of the 'record' named parameter
	 *
	 * @param   MfaTable  $value  The value to validate
	 *
	 * @return  MfaTable
	 * @since   __DEPLOY_VERSION__
	 */
	public function setRecord(MfaTable $value): MfaTable
	{
		if (empty($value))
		{
			throw new DomainException(sprintf('Argument \'record\' of event %s must be a MfaTable object.', $this->name));
		}

		return $value;
	}

	/**
	 * Validate the value of the 'record' named parameter
	 *
	 * @param   Input  $value  The value to validate
	 *
	 * @return  Input
	 * @since   __DEPLOY_VERSION__
	 */
	public function setInput(Input $value): Input
	{
		if (empty($value))
		{
			throw new DomainException(sprintf('Argument \'input\' of event %s must be an Input object.', $this->name));
		}

		return $value;
	}
}
