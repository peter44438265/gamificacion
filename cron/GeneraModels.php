<?php

set_time_limit(1000);
require_once 'zend_conexion.php';

class GeneraModels {

    public $_rutaModel;
    public $_rutaDb;
    public $_modules;
    const AUTOR = 'www.likerow.com(likerow@gmail.com)';
    const TAG_MODEL = 'Model';
    const TAG_FORM = 'Form';
    public $_adapter;

    public function __construct() {
        $this->_rutaModel = APPLICATION_PATH . '/models/Obj/';
        $this->_rutaDb = APPLICATION_PATH . '/models/';
        $this->_modules = APPLICATION_PATH . '/modules/';
        $structure = array(
            '/modules'
        );
        $this->run();
    }

    /**
     * parsea la data de la webservice y valida los productos con QD
     *       
     * @return array() 
     */
    public function run() {
        $db = Zend_Registry::get('db');
        $this->_adapter = $db;
        $tables = $db->listTables();
        foreach ($tables as $table) {
            $nameItems = explode('_', $table);
            if (!file_exists($this->_modules . $nameItems[0])) {
                mkdir($this->_modules . $nameItems[0]);
            }
            $rutaCode = $this->_modules . $nameItems[0] . '/codes/';
            if (!file_exists($rutaCode)) {
                mkdir($rutaCode);
            }
            $rutaModelDb = $rutaCode . 'Model/';
            $rutaModelObj = $rutaCode . 'Obj/';
            $moduleName = ucwords($nameItems[0]);
            if (!file_exists($rutaModelDb)) {
                mkdir($rutaModelDb);
            }
            if (!file_exists($rutaModelObj)) {
                mkdir($rutaModelObj);
            }
            $rutaController = $this->_modules . $nameItems[0] . '/controllers/';
            if (!file_exists($rutaController)) {
                mkdir($rutaController);
            }
            $rutaForm = $rutaCode . 'Form/';
            if (!file_exists($rutaForm)) {
                mkdir($rutaForm);
            }
            $rutaView = $this->_modules . $nameItems[0] . '/views/';
            if (!file_exists($rutaView)) {
                mkdir($rutaView);
            }
            if (!file_exists($rutaView . 'scripts/')) {
                mkdir($rutaView . 'scripts/');
            }
            if (!file_exists($rutaView . 'scripts/index/')) {
                mkdir($rutaView . 'scripts/index/');
            }
            // need to remove underline first, ucwords, and then remove space
            $name = str_replace(' ', "", ucwords(str_replace('_', ' ', $table)));
            // create new class generator
            $class = new Zend_CodeGenerator_Php_Class();
            // configure docblock
            $docblock = new Zend_CodeGenerator_Php_Docblock(array(
                        'shortDescription' => $name . ' model',
                        'tags' => array(
                            array(
                                'name' => 'author',
                                'description' => self::AUTOR,
                            )
                        )
                    ));
            // set name and docblock
            $class->setName('Obj_' . $name);
            $class->setDocblock($docblock);
            // get all fields
            $fields = $db->describeTable($table);
            // want to track primary ids for table
            $primary = array();
            // add to columns each field with a default value
            $columns = array();

            $class->setMethods(
                    array(
                        array(
                            'name' => '__construct',
                            'parameters' => array(
                                array('name' => 'datos'),
                            ),
                            'body' => '
                                    foreach ($datos as $indice => $value){
                                            if(isset ($this->$indice)){
                                                $this->$indice = $value;
                                            }
                                        }
                                    ',
                            'docblock' => new Zend_CodeGenerator_Php_Docblock(array(
                                'shortDescription' => 'contruct ',
                                'tags' => array(
                                    new Zend_CodeGenerator_Php_Docblock_Tag_Param(
                                            array(
                                                'paramName' => 'datos',
                                                'datatype' => 'array'
                                    ))
                                ),
                            )),
                    )));

            foreach ($fields as $field) {
                // if int field default to 0
                $columns[$field['COLUMN_NAME']] =
                        strpos($field['DATA_TYPE'], 'int') !== false ? 0 : "";
                // track primary field(s) for table
                if ($field['PRIMARY']) {
                    $primary[] = $field['COLUMN_NAME'];
                }


                $columname = str_replace(' ', "", ucwords(str_replace('_', ' ', $field['COLUMN_NAME'])));
                $columnameSimple = str_replace(' ', "", str_replace('_', ' ', $field['COLUMN_NAME']));

                $class->setProperty(array(
                    'name' => $field['COLUMN_NAME'],
                    'visibility' => 'protected',
                    'docblock' => array(
                        'tags' => array(
                            new Zend_CodeGenerator_Php_Docblock_Tag(array(
                                'name' => 'var',
                                'description' => 'colums table'
                                    )
                            )
                        )
                    )
                ));
                $class->setMethods(
                        array(
                            new Zend_CodeGenerator_Php_Method(array(
                                'name' => 'get' . $columname,
                                'docblock' => new Zend_CodeGenerator_Php_Docblock(array(
                                    'shortDescription' => 'retorna valor de la columna',
                                    'tags' => array(
                                        new Zend_CodeGenerator_Php_Docblock_Tag_Return(array(
                                            'datatype' => 'string',
                                        ))
                                    ),
                                )),
                                'body' => '
                                  return $this->' . $field['COLUMN_NAME'] . ';
                                  '
                            )),
                            array(
                                'name' => 'set' . $columname,
                                'parameters' => array(
                                    array('name' => $columnameSimple),
                                ),
                                'body' => '
                                    $this->' . $field['COLUMN_NAME'] . ' = $' . $columnameSimple . ';
                                    ',
                                'docblock' => new Zend_CodeGenerator_Php_Docblock(array(
                                    'shortDescription' => 'setea value del columnna',
                                    'tags' => array(
                                        new Zend_CodeGenerator_Php_Docblock_Tag_Param(
                                                array(
                                                    'paramName' => $columnameSimple,
                                                    'datatype' => 'string'
                                        ))
                                    ),
                                )),
                        )));
            }




            file_put_contents($rutaModelObj . $name . '.php', '<?php' . PHP_EOL . $class->generate());
            // create zend_db_table_abstract

            $db_class = new Zend_CodeGenerator_Php_Class();
            $db_class->setName('Model_' . $name);
            $db_class->setDocblock(new Zend_CodeGenerator_Php_Docblock(array(
                        'shortDescription' => $name . ' db table abstract',
                        'tags' => array(
                            array(
                                'name' => 'author',
                                'description' => self::AUTOR,
                            )
                        )
                    )));
            $db_class->setExtendedClass('Zend_Db_Table_Abstract');
            $db_class->setProperty(array(
                'name' => '_name',
                'visibility' => 'protected',
                'defaultValue' => $table,
                'docblock' => array(
                    'tags' => array(
                        new Zend_CodeGenerator_Php_Docblock_Tag(array(
                            'name' => 'var',
                            'description' => 'string Name of db table'
                        ))
                    )
                )
            ));
            if (count($primary)) {
                $pk = count($primary) > 1 ? $primary : $primary[0];
                $db_class->setProperty(array(
                    'name' => '_primary',
                    'visibility' => 'protected',
                    'defaultValue' => $pk,
                    'docblock' => array(
                        'tags' => array(
                            new Zend_CodeGenerator_Php_Docblock_Tag(array(
                                'name' => 'var',
                                'description' => 'string or array of fields in table'
                            ))
                        )
                    )
                ));
            }
            $db_class = $this->getMetodos($db_class, $pk);
            file_put_contents($rutaModelDb . $name . '.php', '<?php' . PHP_EOL . $db_class->generate());

            /**
             * generador de controladores.
             *
             */
            $db_class = new Zend_CodeGenerator_Php_Class();
            $db_class = $this->setController($db_class, $moduleName);

            file_put_contents($rutaController . 'IndexController.php', '<?php' . PHP_EOL . $db_class->generate());

            $db_class = new Zend_CodeGenerator_Php_Class();
            file_put_contents($rutaView . 'scripts/index/index.phtml', '');

            $db_class = new Zend_CodeGenerator_Php_Class();
            $db_class = $this->setForms($db_class, $name, $table);
            file_put_contents($rutaForm . $name . '.php', '<?php' . PHP_EOL . $db_class->generate());
        }
    }

    private function setForms($db_class, $name, $table) {

        $tableData = $this->_adapter->fetchAll("describe $table;");
        $form = '';
        if (!empty($tableData)) {
            foreach ($tableData as $value) {

                $form .= '$element = new Zend_Form_Element_Text(\'' . $value['Field'] . '\');
      $element->setAttribs(array(\'class\' => \'span9\'));
      $element->setLabel(\'' . ucwords(str_replace('_', ' ', $value['Field'])) . '\')
      ->setRequired(true)
      ->addValidator(\'NotEmpty\')
      ->addFilter(\'StringTrim\');
      $this->addElement($element);';
            }
        }
        $db_class->setName('Form_' . $name);
        $db_class->setDocblock(new Zend_CodeGenerator_Php_Docblock(array(
                    'tags' => array(
                        array(
                            'name' => 'author',
                            'description' => self::AUTOR,
                        )
                    )
                )));
        $db_class->setMethods(
                array(
                    // Method passed as concrete instance
                    new Zend_CodeGenerator_Php_Method(array(
                        'name' => 'init',
                        'body' => '

      $this->setAttribs(array(\'class\' => \'form-horizontal\',
      \'id\' => \'form-validate\',
      \'enctype\' => \'multipart/form-data\'));

      ' . $form .
                        '$this->addElement(\'button\', \'submit\', array(
      \'label\' => \'Guardar!\',
      \'type\' => \'submit\',
      \'buttonType\' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_SUCCESS,
      ));

      $this->addElement(\'button\', \'reset\', array(
      \'label\' => \'Cancelar\',
      \'type\' => \'reset\',
      \'buttonType\' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_DANGER,
      ));
      $this->addDisplayGroup(
      array(\'submit\', \'delete\', \'reset\'),
      \'actions\',
      array(
      \'disableLoadDefaultDecorators\' => true,
      \'decorators\' => array(\'Actions\')
      )
      );
      $this->clearDecorators();
      $this->addDecorator(\'FormElements\')
      ->addDecorator(\'HtmlTag\', array(\'tag\' => \'<div>\', \'class\' => \'\'))
      ->addDecorator(\'Form\');

      $this->setElementDecorators(
      array(
      array(\'ViewHelper\'),
      array(\'Errors\'),
      array(\'Description\'),
      array(\'Label\', array(\'separator\' => \' \', \'class\' => \'form-label span3\')),
      array(array(\'row2\' => \'HtmlTag\'), array(\'tag\' => \'<div>\', \'class\' => \'row-fluid\'),),
      array(array(\'row1\' => \'HtmlTag\'), array(\'tag\' => \'<div>\', \'class\' => \'span12\')),
      array(array(\'data\' => \'HtmlTag\'), array(\'tag\' => \'<div>\', \'class\' => \'form-row row-fluid\')),
      )
      );'
                    ))));
        $db_class->setExtendedClass('Twitter_Bootstrap_Form_Horizontal');
        return $db_class;
    }

    private function setController($db_class, $moduleName) {
        $db_class->setName($moduleName . '_IndexController');
        $db_class->setDocblock(new Zend_CodeGenerator_Php_Docblock(array(
                    'tags' => array(
                        array(
                            'name' => 'author',
                            'description' => self::AUTOR,
                        )
                    )
                )));
        $db_class->setMethods(
                array(
                    // Method passed as concrete instance
                    new Zend_CodeGenerator_Php_Method(array(
                        'name' => 'indexAction',
                        'body' => '

      '))));
        $db_class->setExtendedClass('Cit_Controller_Base');
        return $db_class;
    }

    private function getMetodos($object, $primary) {

        $object->setMethods(
                array(
                    // Method passed as concrete instance
                    new Zend_CodeGenerator_Php_Method(array(
                        'name' => 'getAll',
                        'parameters' => array(
                            array('name' => 'order = null'),
                        ),
                        'docblock' => new Zend_CodeGenerator_Php_Docblock(array(
                            'shortDescription' => 'retorna todos los registros de la tabla  ordenado por el parametro indicado',
                            'tags' => array(
                                new Zend_CodeGenerator_Php_Docblock_Tag_Return(array(
                                    'datatype' => 'array|null',
                                )),
                                new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
                                    'paramName' => 'order',
                                    'datatype' => 'string'
                                )),
                            ),
                        )),
                        'body' => '
      $key = $this->_name .\'_all\';
      if (!$result = Cit_Cache::load($key)) {
      $select = $this->getAdapter()->select()
      ->from(array(\'t1\' => $this->_name));
      if (!empty($order))
      $select->order($order);
      $result = $this->getAdapter()->fetchAll($select);
       Cit_Cache::save($result, $key);
      }
      return $result;
      '
                    )),
                    new Zend_CodeGenerator_Php_Method(array(
                        'name' => 'getById',
                        'parameters' => array(
                            array('name' => 'id'),
                        ),
                        'docblock' => new Zend_CodeGenerator_Php_Docblock(array(
                            'shortDescription' => 'retorna la tabla por ID',
                            'tags' => array(
                                new Zend_CodeGenerator_Php_Docblock_Tag_Return(array(
                                    'datatype' => 'array|null',
                                )),
                                new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
                                    'paramName' => 'id',
                                    'datatype' => 'string'
                                )),
                            ),
                        )),
                        'body' => '
      $key = $this->_name .\'_id\'. $id;
      if (!$result = Cit_Cache::load($key)) {
      $select = $this->getAdapter()->select()
      ->from(array(\'t1\' => $this->_name))
      ->where(' . "'$primary = ?'" . ', $id);
      $result = $this->getAdapter()->fetchRow($select);
      Cit_Cache::save($result, $key);
      }
      return $result;
      '
                    )),
                    array(
                        'name' => 'guardar',
                        'parameters' => array(
                            array('name' => 'datos'),
                            array('name' => 'fechaEdicion = TRUE'),
                        ),
                        'body' => '
      if (isset($datos[' . "'" . $primary . "'" . '])) {
      if ($fechaEdicion == TRUE) {
      $datos[\'fecha_edicion\'] = Zend_Date::now()->toString(\'Y-m-d H:i:s\');
      }
      $id = $datos' . $primary . '];
      $where = array("' . $primary . ' = ?" => $id);
      $this->update($datos, $where);
      } else {
      $datos[\'fecha_creacion\'] = Zend_Date::now()->toString(\'Y-m-d H:i:s\');
      $id = $this->insert($datos);
      }
      return $id;',
                        'docblock' => new Zend_CodeGenerator_Php_Docblock(array(
                            'shortDescription' => 'Inserta o actualiza la tabla segun sea el caso',
                            'tags' => array(
                                new Zend_CodeGenerator_Php_Docblock_Tag_Param(
                                        array(
                                            'paramName' => 'datos',
                                            'datatype' => 'array'
                                )),
                                new Zend_CodeGenerator_Php_Docblock_Tag_Param(
                                        array(
                                            'paramName' => 'fechaEdicion',
                                            'datatype' => 'string'
                                )),
                                new Zend_CodeGenerator_Php_Docblock_Tag_Return(array(
                                    'datatype' => 'string',
                                )),
                            ),
                        )),
                )));

        return $object;
    }

}

$obj = new GeneraModels();