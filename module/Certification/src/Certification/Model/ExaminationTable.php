<?php

namespace Certification\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class ExaminationTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->columns(array('id', 'provider', 'id_written_exam', 'practical_exam_id'));
        $sqlSelect->join('written_exam', 'written_exam.id_written_exam = examination.id_written_exam', array('final_score'), 'left')
                ->join('practical_exam', 'practical_exam.practice_exam_id = examination.practical_exam_id', array('practical_total_score', 'Sample_testing_score', 'direct_observation_score'), 'left')
                ->join('provider', 'provider.id=examination.provider', array('certification_id', 'professional_reg_no', 'last_name', 'first_name', 'middle_name', 'certification_reg_no'), 'left')
                ->where(array('add_to_certification' => 'no'));
        $sqlSelect->order('id desc');

        $resultSet = $this->tableGateway->selectWith($sqlSelect);

        return $resultSet;
    }

    public function getExamination($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    
} 