<?php
/**
 * LockDownEnglishPages -- prevents non-staff users from editing the English
 * interface messages on the Inteface Messages Wiki
 *
 * @file
 * @ingroup Extensions
 * @version 0.3.0
 * @date 30 July 2020
 * @author Jack Phoenix
 * @license http://en.wikipedia.org/wiki/Public_domain Public domain
 */

use MediaWiki\MediaWikiServices;

class LockDownEnglishPages {

	/**
	 * @param Title &$title
	 * @param User &$user
	 * @param string $action
	 * @param array|IApiMessage &$result
	 *
	 * @return bool
	 */
	public static function onUserCan( &$title, &$user, $action, &$result ) {
		$pm = MediaWikiServices::getInstance()->getPermissionManager();
		// We want to prevent editing of MediaWiki pages for users who have the
		// editinterface right but who are not staff when the action is 'edit'
		if (
			$title->getNamespace() == NS_MEDIAWIKI &&
			$pm->userHasRight( $user, 'editinterface' ) &&
			!in_array( 'staff', $user->getEffectiveGroups() ) &&
			$action == 'edit'
		) {
			$pageTitle = $title->getDBkey();
			if (
				preg_match( '/\/en/', $pageTitle ) || // page title has /en in it
				!preg_match( '/\//', $pageTitle ) // page title has no / in it
			) {
				$result = false;
				return false;
			}
		}
		return true;
	}
}
