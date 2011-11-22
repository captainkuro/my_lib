<?php
/*
$regex = '/<SCRIPT Language=VBScript><!--[^<]*<\\/SCRIPT><!--((?!-->).)*-->/ims';
$text = file_get_contents('infected.html.txt');
preg_match_all($regex, $text, $match);
print_r($match);
echo preg_replace($regex, '', $text);
*/

require_once 'FolderWalker.class.php';
class VBRemover extends FolderWalker {
	
	public static function factory() {
		return new VBRemover();
	}
	
	protected function folderFilter(DirectoryIterator $pParent, DirectoryIterator $pNode) {
		return false;
	}
	
	protected function processFolder(DirectoryIterator $pParent, DirectoryIterator $pNode) {
	}
	
	// Berekstensi .html .htm .phtml
	protected function fileFilter(DirectoryIterator $pParent, DirectoryIterator $pNode) {
		$info = pathinfo($pNode->getFilename());
		return in_array($info['extension'], array('html', 'htm', 'phtml'));
	}
	
	protected function processFile(DirectoryIterator $pParent, DirectoryIterator $pNode) {
		$regex = '/<SCRIPT Language=VBScript><!--[^<]*<\\/SCRIPT><!--((?!-->).)*-->/ims';
		$text = file_get_contents($pNode->getPathname());
		$filtered = preg_replace($regex, '', $text);
		if ($text != $filtered) file_put_contents($pNode->getPathname(), $filtered);
	}
}

echo VBRemover::factory()->startWalking('D:\\temp\pulsa');