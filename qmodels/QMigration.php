<?php
namespace quarsintex\quartronic\qmodels;

class QMigration extends \quarsintex\quartronic\qcore\QModel
{
    protected function loadStructure()
    {
        parent::loadStructure();
        if (!$this->_structure) {
            $this->_structure['name'] = 'TEXT';
            $this->_structure['applied_at'] = 'TIMESTAMP';
        }
    }
}

?>