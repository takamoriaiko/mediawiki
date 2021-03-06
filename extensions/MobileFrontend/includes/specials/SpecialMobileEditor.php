<?php

class SpecialMobileEditor extends MobileSpecialPage {
	public function __construct() {
		parent::__construct( 'MobileEditor' );
		$this->listed = false;
	}

	public function executeWhenAvailable( $subpage ) {
		wfProfileIn( __METHOD__ );

		$title = Title::newFromText( $subpage );

		if ( is_null( $title )) {
			$this->showPageNotFound();
			return;
		}

		$data = $this->getRequest()->getValues();
		unset( $data['title'] ); // Remove the title of the special page

		$section = (int)$this->getRequest()->getVal( 'section', 0 );

		$output = $this->getOutput();
		$output->addModules( 'mobile.special.mobileeditor.scripts' );
		$output->setPageTitle( $this->msg( 'mobile-frontend-editor-redirect-title' )->text() );

		$context = MobileContext::singleton();
		$articleUrl = $context->getMobileUrl( $title->getFullURL( $data ) );
		$targetUrl = $articleUrl . '#editor/' . $section;

		$html =
			Html::openElement( 'div',
				array(
					'id' => 'mw-mf-editor',
					'data-targeturl' => $targetUrl
				)
			) .
			Html::openElement( 'noscript' ) .
			Html::openElement( 'div',
				array(
					'class' => 'error alert',
				)
			) .
			Html::element( 'h2', array(),
				$this->msg( 'mobile-frontend-editor-unavailable-header' )->text() ) .
			Html::element( 'p', array(),
				$this->msg( 'mobile-frontend-editor-unavailable' )->text() ) .
			Html::openElement( 'p' ) .
				Html::element( 'a',
					array( 'href' => $title->getLocalUrl() ),
					$this->msg( 'returnto', $title->getText() )->text() ) .
			Html::closeElement( 'p' ) .
			Html::closeElement( 'div' ) . // .error .alert
			Html::closeElement( 'noscript' ) .
			Html::closeElement( 'div' ); // #mw-mf-editorunavailable

		$output->addHTML( $html );

		wfProfileOut( __METHOD__ );
	}
}
