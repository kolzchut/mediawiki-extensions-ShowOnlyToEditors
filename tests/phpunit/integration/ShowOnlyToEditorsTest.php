<?php

namespace MediaWiki\Extension\ShowOnlyToEditors\Tests\Integration;

use MediaWiki\Context\RequestContext;
use MediaWiki\Page\PageReferenceValue;
use MediaWiki\User\User;
use MediaWikiIntegrationTestCase;
use ParserOptions;

/**
 * @group Database
 * @covers \MediaWiki\Extension\ShowOnlyToEditors\Hooks
 */
class ShowOnlyToEditorsTest extends MediaWikiIntegrationTestCase {

	private const WIKITEXT = 'A{{#showonlytoeditors:EditorOnlyMarker}}B';

	protected function setUp(): void {
		parent::setUp();
		// Only the test group may edit, whatever the wiki's own settings say.
		$this->setGroupPermissions( [
			'*' => [ 'edit' => false ],
			'user' => [ 'edit' => false ],
			'showonlytoeditors-test' => [ 'edit' => true ],
		] );
	}

	private function getEditor(): User {
		return $this->getTestUser( [ 'showonlytoeditors-test' ] )->getUser();
	}

	private function parse( ParserOptions $options ): string {
		$parser = $this->getServiceContainer()->getParserFactory()->getInstance();
		return $parser->parse(
			self::WIKITEXT,
			PageReferenceValue::localReference( NS_MAIN, 'ShowOnlyToEditorsTest' ),
			$options
		)->getRawText();
	}

	public function testAnonymousOptionsHideTextEvenWhenAnEditorMakesTheRequest() {
		RequestContext::getMain()->setUser( $this->getEditor() );

		$html = $this->parse( ParserOptions::newFromAnon() );

		$this->assertStringNotContainsString( 'EditorOnlyMarker', $html );
	}

	public function testEditorOptionsShowTextEvenWhenTheRequestIsAnonymous() {
		RequestContext::getMain()->setUser( new User() );

		$html = $this->parse( ParserOptions::newFromUser( $this->getEditor() ) );

		$this->assertStringContainsString( 'EditorOnlyMarker', $html );
	}

	public function testNonEditorOptionsHideText() {
		$html = $this->parse( ParserOptions::newFromUser( $this->getTestUser()->getUser() ) );

		$this->assertStringNotContainsString( 'EditorOnlyMarker', $html );
	}

	public function testEditorsAndNonEditorsGetDifferentCacheKeys() {
		$anonHash = ParserOptions::newFromAnon()->optionsHash( [] );
		$editorHash = ParserOptions::newFromUser( $this->getEditor() )->optionsHash( [] );

		$this->assertNotSame( $anonHash, $editorHash );
		$this->assertStringContainsString( '!editor', $editorHash );
	}
}
