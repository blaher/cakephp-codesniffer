<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://pear.php.net/package/PHP_CodeSniffer_CakePHP
 * @since         CakePHP CodeSniffer 1.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace CakePHP\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Ensures doc block alignments.
 */
class DocBlockAlignmentSniff implements Sniff
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        return [T_DOC_COMMENT_OPEN_TAG];
    }

    /**
     * {@inheritDoc}
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $commentClose = $phpcsFile->findNext(T_DOC_COMMENT_CLOSE_TAG, $stackPtr);
        $afterComment = $phpcsFile->findNext(T_WHITESPACE, $commentClose + 1, null, true);
        $commentIndentation = $tokens[$stackPtr]['column'] - 1;
        $nextIndentation = $tokens[$afterComment]['column'] - 1;
        if ($commentIndentation != $nextIndentation) {
            $msg = 'Doc block not aligned with code; expected indentation of %s but found %s';
            $data = [$nextIndentation, $commentIndentation];
            $fix = $phpcsFile->addFixableError($msg, $stackPtr, 'DocBlockMisaligned', $data);
            if ($fix === true) {
                $indent = str_repeat(' ', $nextIndentation);
                if ($commentIndentation === 0) {
                    $phpcsFile->fixer->addContentBefore($stackPtr, $indent);
                } else {
                    $phpcsFile->fixer->replaceToken($stackPtr - 1, $indent);
                }
            }
        }
    }
}
