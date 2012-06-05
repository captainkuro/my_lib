<?php
/**
 * Wrapper for DataTables communication
 * 
 * Limitation: currently only supports one column sorting
 * @link http://datatables.net/
 * 
 * @author Khandar William
 */
abstract class Datatables
{
	/**
	 * Every DataTables request includes an id which will be sent back in reply
	 * @var string
	 */
	protected $_id;
	/******************************************
	 * For pagination
	 ******************************************/
	protected $_offset;
	protected $_limit;
	/******************************************
	 * For ordering
	 ******************************************/
	/**
	 * Order by which column
	 * @var string the column name
	 */
	protected $_orderColumn;
	/**
	 * Determine ascending or descending order
	 * @var string either 'asc' or 'desc'
	 */
	protected $_orderDirection;
	/**
	 * Hold information about which columns could be sorted on
	 * @var array [column_name] => bool
	 */
	protected $_sortable;
	/******************************************
	 * For filtering
	 ******************************************/
	/**
	 * The global search term
	 * @var string
	 */
	protected $_search;
	/**
	 * Hold information on individual column filtering (if any)
	 * @var array
	 */
	protected $_columnSearch;
	/**
	 * Hold information about which columns could be searched on
	 * @var array [column_name] => bool
	 */
	protected $_searchable;
	 
	/**
	 * Receive the dataTables parameters
	 * @param array $params the parameters from datatables
	 */
	public function __construct($params)
	{
		/* 
		Class ini diextend sesuai keperluan per DataTables
		Prerequisuite:
		jumlah dan urutan kolom diketahui backend dan frontend
		
		Input:
		sEcho
		iDisplayStart
		iDisplayLength
		iSortingCols
		iSortCol_0
		sSortDir_0
		bSortable_*
		sSearch
		bSearchable_*
		sSearch_*
		
		Output:
		sEcho
		iTotalRecords
		iTotalDisplayRecords
		aaData
		*/
		// ID
		$this->_id = isset($params['sEcho']) ? $params['sEcho'] : 0;
		// Pagination
		if (isset($params['iDisplayStart']) && $params['iDisplayLength'] != '-1') {
			$this->_offset = (int)$params['iDisplayStart'];
			$this->_limit = (int)$params['iDisplayLength'];
		} else {
			$this->_offset = 0;
			$this->_limit = null;
		}
		$columns = $this->getColumns();
		// Ordering
		if (isset($params['iSortCol_0'])) {
			$columnSeq = intval($params['iSortCol_0']);
			// assume only 1 column
			if (isset($params['bSortable_'.$columnSeq]) && $params['bSortable_'.$columnSeq] == "true") {
				$this->_orderColumn = $columns[$columnSeq];
				$this->_orderDirection = isset($params['sSortDir_0']) ? $params['sSortDir_0'] : 'asc';
			} else {
				$this->_orderColumn = null;
				$this->_orderDirection = null;
			}
			
			if (isset($params['bSortable_0'])) {
				$this->_sortable = array();
				foreach ($columns as $i => $c) {
					$this->_sortable[$c] = isset($params['bSortable_'.$i]) && $params['bSortable_'.$i] == 'true';
				}
			} else {
				// Default all columns are sortable
				$this->_sortable = array_combine($columns, array_fill(0, count($columns), true));
			}
		}
		// Filtering
		if (isset($params['sSearch']) && (bool)$params['sSearch']) {
			$this->_search = $params['sSearch'];
		} else {
			$this->_search = null;
		}
		$this->_columnSearch = array();
		$this->_searchable = array();
		foreach ($columns as $i => $c) {
			if (isset($params['sSearch_'.$i]) && (bool)$params['sSearch_'.$i]) {
				$this->_columnSearch[$c] = $params['sSearch_'.$i];
			}
			$this->_searchable[$c] = isset($params['bSearchable_'.$i]) && $params['bSearchable_'.$i] == 'true';
		}
	}
	
	/**
	 * Return the JSON string of result
	 */
	public function result()
	{
		$json = array(
			'sEcho' => $this->_id,
			'iTotalRecords' => $this->getTotalRecords(),
			'iTotalDisplayRecords' => $this->getTotalDisplayRecords(),
			'aaData' => $this->retrieveData(),
		);
		return json_encode($json);
	}
	
	/**
	 * Return an array containing list of column names used by this class
	 * @return array sequential
	 */
	abstract public function getColumns();
	
	/**
	 * Return the total number of data without any filtering
	 * @return int|string
	 */
	abstract public function getTotalRecords();
	
	/**
	 * Return the total number of data with current filtering (if any)
	 * @return int|string
	 */
	abstract public function getTotalDisplayRecords();
	
	/**
	 * Return the data based on DataTables parameters acquired
	 * @return array an array of rows as sequential arrays
	 */
	abstract public function retrieveData();
}
 