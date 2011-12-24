<?php 
/**
 * an abstract class that walk recursively through a given path.
 * Activating the walking can be done by calling the StartWalking($pathtofolder) function
 * This class contains 4 abstract function that the user needs to be implemented:
 * 1. shouldProcessFolder: a function that will be called when walking process finds a folder. Expected to return true of false
 * 2. processFolder: a function that will be called if shouldProcessFolder function returns true
 * 3. shouldProcessFile: behave the same as FolderFilter but instead of folder, this function subject is file
 * 4. processFile: a function that will be called if shouldProcessFile returns true
 * 5. shouldBrowseFolder: a function that will be called to decide whether or not browse a folder
 */
abstract class FolderWalker {

	public $mFolderPath;
	public $mDirIter;
	const  DS = DIRECTORY_SEPARATOR;
	
	public function __construct() {
	}
	
	/**
	 * the function to activate walking process. Default setting is walk recursively, but can be change easily
	 */
	public function startWalking($pFolderPath, $pRecursiveWalk = true) {
		$this->mFolderPath = $pFolderPath;	
		$this->mDirIter = new DirectoryIterator($this->mFolderPath);
		return $this->walkDirectory($this->mDirIter, $pRecursiveWalk);
	}

	/**
	 * the recursive function that walks through a directory of given path
	 */
	protected function walkDirectory(DirectoryIterator $Directory, $pRecursiveWalk, $depth = 0) {
		$mStringFolderTree = str_repeat(' ', ($depth * 5)) . basename($Directory->getPath()) . self::DS . "\n";
		while ($Directory->valid()) {
			$node = $Directory->current();
			if ($node->isDir() && $node->isReadable() && !$node->isDot()) {
				if ($this->shouldProcessFolder($Directory, $node)) {
					$this->processFolder($Directory, $node);				
				}
				if ($pRecursiveWalk && $node->isReadable() && $this->shouldBrowseFolder($Directory, $node)) {
					try {
						$mStringFolderTree .= $this->walkDirectory(new DirectoryIterator($node->getPathname()), $pRecursiveWalk, $depth + 1);
					} catch (Exception $e) {
						echo "Failed to open " . $node->getPathname() . "\n";
					}
				}
			}
			elseif ($node->isFile()) {
				if($this->shouldProcessFile($Directory, $node)) {
					$this->processFile($Directory, $node);
				}
				$mStringFolderTree .= str_repeat(' ', ((1 + $depth) * 5)) . $node->getFilename() . "\n";
			}
			$Directory->next();
		}
		return $mStringFolderTree;
	}
	
	/**
	 * Should we process this directory?
	 * User need to implement this. Expected to return true or false
	 */
	abstract protected function shouldProcessFolder(DirectoryIterator $pParent, DirectoryIterator $pNode);
	
	/**
	 * What should we do with this directory?
	 * User need to implement this. This is a procedure
	 */
	abstract protected function processFolder(DirectoryIterator $pParent, DirectoryIterator $pNode);
	
	/**
	 * Should we process this file?
	 * User need to implement this. Expected to return true or false
	 */
	abstract protected function shouldProcessFile(DirectoryIterator $pParent, DirectoryIterator $pNode);
	
	/**
	 * What should we do with this file?
	 * User need to implement this. This is a procedure
	 */
	abstract protected function processFile(DirectoryIterator $pParent, DirectoryIterator $pNode);
	
	/**
	 * Should we browse this directory?
	 * User need to implement this. Expected to return true or false
	 */
	abstract protected function shouldBrowseFolder(DirectoryIterator $pParent, DirectoryIterator $pNode);
}