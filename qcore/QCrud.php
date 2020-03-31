<?php
namespace quarsintex\quartronic\qcore;

use yii\db\Exception;

class QCrud extends QSource
{
  protected $model;
  public $page = 1;
  public $limit = 10;

    protected function getConnectedParams()
    {
        return [
            'rootDir' => &self::$Q->rootDir,
        ];
    }

  function __construct($modelName)
  {
      $controllerPath = $this->rootDir . 'qmodels/' . $modelName . '.php';
      if (file_exists($controllerPath)) {
          $modelClass = '\\quarsintex\\quartronic\\qmodels\\'.$modelName;
          $this->model = new $modelClass;
      } else {
          $this->model = new \quarsintex\quartronic\qcore\QModel(strtolower($modelName));
      }
      $this->page = intval(self::$Q->request->getParam('page', $this->page));
  }

  function getOffset() {
      return $this->limit * ($this->page - 1);
  }

  function getModelFields() {
      return $this->model->getFieldList();
  }

  function getList() {
      $model = $this->model;
      if ($this->limit) {
          $model->query->limit($this->limit)->offset($this->offset);
      }
      return $model->all;
  }

  function create($params)
  {
      $this->model->fields = $params;
      $this->model->save();
  }

  function view($params)
  {
      if (empty($params['id'])) return null;
      return $this->model->find($params);
  }

  function update($params)
  {
      if (empty($params['id'])) return null;
      $this->model = $this->model->findByPk($params);
      $this->model->fields = $params;
      $this->model->save();
  }

  function delete($params)
  {
      if (empty($params['id'])) return null;
      $this->model = $this->model->findByPk($params);
      $this->model->delete();
  }

  static function getAutoStructure() {
     return [
        'user' => [],
        'group' => [],
        'role' => [],
        'section' => [],
     ];
  }
}

?>