<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2022 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\User;

\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;

/**
 * Trait for classes which require a user to work with.
 *
 * @since  __DEPLOY_VERSION__
 */
trait CurrentUserTrait
{
	/**
	 * The current user object.
	 *
	 * @var    User
	 * @since  __DEPLOY_VERSION__
	 */
	private $currentUser;

	/**
	 * Returns the current user, if none is set the identity of the global app
	 * is returned. This will change in 5.0 and an empty user will be returned.
	 *
	 * @return  User
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getCurrentUser(): User
	{
		if (!$this->currentUser)
		{
			@trigger_error(
				sprintf('User must be set in %s. This will not be caught anymore in 5.0', __METHOD__),
				E_USER_DEPRECATED
			);
			$this->currentUser = Factory::getApplication()->getIdentity() ?: new User;
		}

		return $this->currentUser;
	}

	/**
	 * Sets the current user.
	 *
	 * @param   User  $currentUser  The current user object
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setCurrentUser(User $currentUser): void
	{
		$this->currentUser = $currentUser;
	}
}
