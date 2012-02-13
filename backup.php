<?php
// zip and backup manga
require 'FolderWalker.class.php';

class Backup_Manga extends FolderWalker {
	// protected $source = 'D:\Manga\# By Author';
	protected $source = 'D:\Manga\# By Volume';
	// protected $dest = 'F:\Manga\# By Author';
	protected $dest = 'F:\Manga\# By Volume';

	protected function shouldProcessFolder(DirectoryIterator $pParent, DirectoryIterator $pNode) {
		return true;
	}
	
	protected function processFolder(DirectoryIterator $pParent, DirectoryIterator $pNode) {
		$pNode = new DirectoryIterator($pNode->getPathname());
		$tSource = $this->source;
		$tDest = $this->dest;
		$this->source = $tSource.'\\'.$pParent->getBasename();
		$this->dest = $tDest.'\\'.$pParent->getBasename();
		if (!is_dir($this->dest)) mkdir($this->dest);
		while ($pNode->valid()) {
			$node = $pNode->current();
			if ($node->isDir() && $node->isReadable() && !$node->isDot()) {
				$this->backupFolder($pNode, $node);
			}
			$pNode->next();
		}
		$this->source = $tSource;
		$this->dest = $tDest;
	}
	
	protected function backupFolder(DirectoryIterator $pParent, DirectoryIterator $pNode) {
		$p7z = '"E:\Program Files\7-Zip\7z.exe" a -tzip -mx0';
		$inpath = '"'.$this->source.'\\'.$pNode->getBasename().'\\*"';
		$outfile = '"'.$this->dest.'\\'.$pNode->getBasename().'.zip"';
		echo "$p7z $outfile $inpath\r\n";
	}
	
	protected function shouldProcessFile(DirectoryIterator $pParent, DirectoryIterator $pNode) {
		return false;
	}
	
	protected function processFile(DirectoryIterator $pParent, DirectoryIterator $pNode) {
	}
	
	protected function shouldBrowseFolder(DirectoryIterator $pParent, DirectoryIterator $pNode) {
		return false;
	}
}

$a = new Backup_Manga;
// $a->startWalking('D:\Manga\# By Author', false);
$a->startWalking('D:\Manga\# By Volume', false);