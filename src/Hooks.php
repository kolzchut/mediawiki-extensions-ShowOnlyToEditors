<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * @file
 */

namespace MediaWiki\Extension\ShowOnlyToEditors;

use MediaWiki\MediaWikiServices;
use MediaWiki\User\UserIdentity;
use Parser;
use User;

class Hooks implements
	\MediaWiki\Hook\ParserFirstCallInitHook,
	\MediaWiki\Hook\PageRenderingHashHook
{

	/**
	 * Register parser hooks.
	 *
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserFirstCallInit
	 * @see https://www.mediawiki.org/wiki/Manual:Parser_functions
	 *
	 * @param Parser $parser
	 *
	 * @throws \MWException
	 */
	public function onParserFirstCallInit( $parser ) {
		// {{#showOnlyToEditors: text }}
		$parser->setFunctionHook( 'showonlytoeditors', [ self::class, 'parserFunctionShowOnlyToEditors' ] );
	}

	/**
	 * Parser function handler for {{#showOnlyToEditors: .. | .. }}
	 *
	 * @param Parser $parser
	 * @param string $text
	 *
	 * @return string HTML to insert in the page.
	 */
	public static function parserFunctionShowOnlyToEditors( Parser $parser, string $text
	): string {
		$user = $parser->getUser();
		return self::userIsEditor( $user ) ? $text : '';
	}

	/**
	 * Use this hook to alter the parser cache option hash key. A parser extension
	 * which depends on user options should install this hook and append its values to
	 * the key.
	 *
	 * @param string &$confstr Reference to a hash key string which can be modified
	 * @param User $user User requesting the page
	 * @param array &$forOptions Array of options the hash is for
	 *
	 * @return void True or no return value to continue or false to abort
	 * @since 1.35
	 *
	 */
	public function onPageRenderingHash( &$confstr, $user, &$forOptions ) {
		if ( self::userIsEditor( $user ) ) {
			$confstr .= "!editor";
		}
	}

	/**
	 * @param UserIdentity $user
	 *
	 * @return bool
	 */
	private static function userIsEditor( UserIdentity $user ): bool {
		$services = MediaWikiServices::getInstance();
		$permissionManager = $services->getPermissionManager();
		return $permissionManager->userHasRight( $user, 'edit' );
	}
}
