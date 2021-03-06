<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_service extends CI_Model {

  function getData($postData=null){

      $response = array();

      ## Read value
      $draw = $postData['draw'];
      $start = $postData['start'];
      $rowperpage = $postData['length']; // Rows display per page
      $columnIndex = $postData['order'][0]['column']; // Column index
      $columnName = $postData['columns'][$columnIndex]['data']; // Column name
      $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
      $searchValue = $postData['search']['value']; // Search value

      ## Search 
      $searchQuery = "";
      if($searchValue != ''){
          $searchQuery = " (nama like '%".$searchValue."%' or 
                email like '%".$searchValue."%' or 
                hp like '%".$searchValue."%' or 
                alamat like '%".$searchValue."%' or 
                pesan like '%".$searchValue."%' ) ";
      }


      ## Total number of records without filtering
      $this->db->select('count(*) as allcount');
      $records = $this->db->from('tbl_hubungikami')
      ->get()
      ->result();
      $totalRecords = $records[0]->allcount;

      ## Total number of record with filtering
      $this->db->select('count(*) as allcount');
      if($searchQuery != '')
      $this->db->where($searchQuery);
      $records = $this->db->from('tbl_hubungikami')
      ->get()
      ->result();
      $totalRecordwithFilter = $records[0]->allcount;

      
      ## Fetch records
      $this->db->select('*');
      if($searchQuery != '')
      $this->db->where($searchQuery);
      $this->db->order_by($columnName, $columnSortOrder);
      $this->db->limit($rowperpage, $start);
      $records = $this->db->from('tbl_hubungikami')
      ->get()
      ->result();

      $data = array();
      foreach($records as $record ){
         
          $data[] = array( 
              "id"=>++$start,
              "nama"=>$record->nama,
              "pesan"=>$record->pesan,
              "reply"=>$record->reply,
              "status"=>$record->status,
              "tanggal"=>$record->tanggal,
              "no"=>$record->id
          ); 
      }

      ## Response
      $response = array(
          "draw" => intval($draw),
          "iTotalRecords" => $totalRecords,
          "iTotalDisplayRecords" => $totalRecordwithFilter,
          "aaData" => $data
      );

      return $response; 
  }

}