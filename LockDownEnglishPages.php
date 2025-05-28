<?php
/**
 * LockDownEnglishPages -- prevents non-staff users from editing the English
 * interface messages on the Interface Messages Wiki
 *
 * @file
 * @ingroup Extensions
 * @version 0.4
 * @date 29 May 2025
 * @author Jack Phoenix
 * @license http://en.wikipedia.org/wiki/Public_domain Public domain
 */

use MediaWiki\Permissions\Hook\GetUserPermissionsErrorsHook;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\User\UserGroupManager;

class LockDownEnglishPages implements GetUserPermissionsErrorsHook {
	/**
	 * @var UserGroupManager
	 */
	private $userGroupManager;

	/**
	 * @var PermissionManager
	 */
	private $permissionManager;

	/**
	 * @param UserGroupManager $userGroupManager
	 * @param PermissionManager $permissionManager
	 */
	public function __construct( UserGroupManager $userGroupManager, PermissionManager $permissionManager ) {
		$this->userGroupManager = $userGroupManager;
		$this->permissionManager = $permissionManager;
	}

	/**
	 * @param Title $title
	 * @param User $user
	 * @param string $action
	 * @param string &$result
	 *
	 * @return bool
	 */
	public function onGetUserPermissionsErrors( $title, $user, $action, &$result ) {
		// We want to prevent editing of MediaWiki pages for users who have the
		// editinterface right but who are not staff when the action is 'edit'
		if (
			$title->getNamespace() == NS_MEDIAWIKI &&
			$this->permissionManager->userHasRight( $user, 'editinterface' ) &&
			!in_array( 'staff', $this->userGroupManager->getUserEffectiveGroups( $user ) ) &&
			$action == 'edit'
		) {
			$pageTitle = $title->getDBkey();
			if (
				preg_match( '/\/en/', $pageTitle ) || // page title has /en in it
				!preg_match( '/\//', $pageTitle ) // page title has no / in it
			) {
				$result = 'lockdownenglishpages-error-not-staff';
				return false;
			}
		}
		return true;
	}
}
