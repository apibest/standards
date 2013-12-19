<?php
/**
 * Parses and verifies the doc comments for functions.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @author    Felix Brandt <mail@felixbrandt.de>
 * @author    Taras Omelianenko <t.omelianenko@dicslab.com>
 * @author    Denis Alexeev <d.alexeev@dicslab.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @copyright 2013-2014 DICSLAB LLC (EDRPOU 38879176)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_CommentParser_FunctionCommentParser', true) === false) {
    $error = 'Class PHP_CodeSniffer_CommentParser_FunctionCommentParser not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Parses and verifies the doc comments for functions.
 *
 * Verifies that :
 * <ul>
 *  <li>A comment exists</li>
 *  <li>There is a blank newline after the short description</li>
 *  <li>There is a blank newline between the long and short description</li>
 *  <li>There is a blank newline between the long description and tags</li>
 *  <li>Parameter names represent those in the method</li>
 *  <li>Parameter comments are in the correct order</li>
 *  <li>Parameter comments are complete</li>
 *  <li>A type hint is provided for array and custom class</li>
 *  <li>Type hint matches the actual variable/class type</li>
 *  <li>A blank line is present before the first and after the last parameter</li>
 *  <li>A return type exists</li>
 *  <li>Any throw tag must have a comment</li>
 *  <li>The tag order and indentation are correct</li>
 * </ul>
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @author    Felix Brandt <mail@felixbrandt.de>
 * @author    Taras Omelianenko <t.omelianenko@dicslab.com>
 * @author    Denis Alexeev <d.alexeev@dicslab.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @copyright 2013-2014 DICSLAB LLC (EDRPOU 38879176)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class DICSLAB_Sniffs_Commenting_FunctionCommentSniff extends PEAR_Sniffs_Commenting_FunctionCommentSniff
{
    /**
     * Process the see tags.
     *
     * @param int $commentStart The position in the stack where the comment started.
     *
     * @return void
     */
    protected function processSees($commentStart)
    {
        if ($this->isInheritDoc()) {
            return;
        }

        $sees = $this->commentParser->getSees();
        if (empty($sees) === false) {
            $tagOrder = $this->commentParser->getTagOrders();
            $index    = array_keys($this->commentParser->getTagOrders(), 'see');
            foreach ($sees as $i => $see) {
                $errorPos = ($commentStart + $see->getLine());
                $since    = array_keys($tagOrder, 'since');
                if (count($since) === 1 && $this->_tagIndex !== 0) {
                    $this->_tagIndex++;
                    if ($index[$i] !== $this->_tagIndex) {
                        $error = 'The @see tag is in the wrong order; the tag precedes @return';
                        $this->currentFile->addError($error, $errorPos, 'SeeOrder');
                    }
                }

                $content = $see->getContent();
                if (empty($content) === true) {
                    $error = 'Content missing for @see tag in function comment';
                    $this->currentFile->addError($error, $errorPos, 'EmptySee');
                    continue;
                }

                $spacing = substr_count($see->getWhitespaceBeforeContent(), ' ');
                if ($spacing !== 4) {
                    $error = '@see tag indented incorrectly; expected 4 spaces but found %s';
                    $data  = array($spacing);
                    $this->currentFile->addError($error, $errorPos, 'SeeIndent', $data);
                }
            }//end foreach
        }//end if

    }//end processSees()


    /**
     * Process the return comment of this function comment.
     *
     * @param int $commentStart The position in the stack where the comment started.
     * @param int $commentEnd   The position in the stack where the comment ended.
     *
     * @return void
     */
    protected function processReturn($commentStart, $commentEnd)
    {
        if ($this->isInheritDoc()) {
            return;
        }

        parent::processReturn($commentStart, $commentEnd);
    }//end processReturn()


    /**
     * Process any throw tags that this function comment has.
     *
     * @param int $commentStart The position in the stack where the comment started.
     *
     * @return void
     */
    protected function processThrows($commentStart)
    {
        if ($this->isInheritDoc()) {
            return;
        }

        parent::processThrows($commentStart);
    }//end processThrows()


    /**
     * Process the function parameter comments.
     *
     * @param int $commentStart The position in the stack where
     *                          the comment started.
     * @param int $commentEnd   The position in the stack where
     *                          the comment ended.
     *
     * @return void
     */
    protected function processParams($commentStart)
    {
        if ($this->isInheritDoc()) {
            return;
        }

        parent::processParams($commentStart);
    }//end processParams()


    /**
     * Process a list of unknown tags.
     *
     * @param int $commentStart The position in the stack where the comment started.
     * @param int $commentEnd   The position in the stack where the comment ended.
     *
     * @return void
     */
    protected function processUnknownTags($commentStart, $commentEnd)
    {

    }//end processUnknownTags

    /**
     * Is the comment an inheritdoc?
     *
     * @return boolean True if the comment is an inheritdoc
     */
    protected function isInheritDoc ()
    {
        $content = $this->commentParser->getComment()->getContent();

        return preg_match('#{@inheritdoc}#i', $content) === 1;
    } // end isInheritDoc()


}//end class
