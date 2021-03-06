<?php

/**
 * actual class...
 */
class SpecialCreatePage extends SpecialPage
{
	var $myParser;

    function __construct() {
        parent::__construct('CreatePage');
		$this->myParser = new Parser();
		$this->mIncludable = true;
    }

	function execute( $par ) {
		global $wgOut, $wgRequest;
		global $wgCreatePageNamespaces, $wgCreatePageTypes;

		$namespaces = '';
		$types = '';
		$newtitle = '';
		$suffix1 = '';
		$suffix2 = '';

		// assemble types and namespaces
		foreach( $wgCreatePageNamespaces as $key => $value ) {
			if ( $value )
				$namespaces .= '<option selected>' . $key;
			else
				$namespaces .= '<option>' . $key;
		}
		foreach( $wgCreatePageTypes as $key => $value ) {
			if ( $value )
				$types .= '<option selected>' . $key;
			else
				$types .= '<option>' . $key;
		}

		$this->setHeaders();

		if ( $wgRequest->getBool('was_submitted', false ) ) {
			$ns = $wgRequest->getVal( 'namespace' );
			$newtitle = $wgRequest->getVal( 'newtitle' );
			$type = $wgRequest->getVal( 'type' );
			$suffix1 = $wgRequest->getVal( 'suffix1' );
			$suffix2 = $wgRequest->getVal( 'suffix2' );

			if ( $ns != '' && $newtitle != '' && $type != '' && $suffix1 != '' ) {

				// assemble title:
				$completeTitle = $ns . ':' . $newtitle . ' ' . $type . ' ' . '(' . $suffix1;
				if ( $suffix2 != '' ) {
					$completeTitle .= ', ' . $suffix2;
				}
				$completeTitle .= ')';


				$redir = Title::newFromText( $completeTitle );
				if ( $redir->exists() )
					$wgOut->redirect($redir->getFullURL() );
				else
					$wgOut->redirect($redir->getFullURL() . '?action=edit' );
			} else {
				$this->addText( wfMessage('missing_input')->text() );
			}
		}

		$this->addText( wfMessage('introduction')->text() );

// caused problems 2007-12-09
		$wgOut->setPagetitle( wfMessage('emCreatePagePageTitle')->text() );

		global $wgScript;
		$handler = $wgScript . '/' . MWNamespace::getCanonicalName(NS_SPECIAL) . ":" . SpecialPage::getLocalName( 'Create page' );

		$wgOut->addHTML('<form name=\'new_page\' method=\'get\' action=\'' . $handler . '\'>
				<input type="hidden" name="was_submitted" value="true">
				<table>
					<tr>
						<th>' . wfMessage('namespace_header')->text() . '</th>
						<th>' . wfMessage('newpage_title')->text() . '</th>
						<th>' . wfMessage('newpage_type')->text() . '</th>
						<th>' . wfMessage('newpage_suffix1')->text() . '</th>
						<th>' . wfMessage('newpage_suffix2')->text() . '</th>
					<tr>
						<td><select name=namespace>' . $namespaces . '</select></td>
						<td><input size=40 type=\'text\' name=\'newtitle\' value=\'' . $newtitle . '\'></td>
						<td><select name=type>' . $types . '</select></td>
						<td><input size=15 type=\'text\' name=\'suffix1\' value=\'' . $suffix1 . '\'></td>
						<td><input size=22 type=\'text\' name=\'suffix2\' value=\'' . $suffix2 . '\'></td>
						<td><input type=\'submit\' value=\'' . wfMessage('button')->text()  . '\'></td>
					</tr>
					<tr>
						<td></td>
						<td>' . wfMessage( 'newtitle_desc' )->text() . '</td>
						<td></td>
						<td>' . wfMessage( 'suffix1_desc' )->text() . '</td>
						<td></td>
					</tr>
				</table></form>');
	}

	/**
	 * parse the text because we can't add WikiText. see here:
	 *  http://bugzilla.wikimedia.org/show_bug.cgi?id=9762
	 */
	function addText( $text ) {
		global $wgTitle, $wgOut;
		$po = $this->myParser->parse( $text, $wgTitle, new ParserOptions(), false, true );
		$wgOut->addHTML( $po->getText() );
	}
}

?>
