<?php

namespace {
    function Q()
    {
        static $Q;
        if (!$Q) $Q = new \quarsintex\quartronic\qcore\QGlobalCallback;
        return $Q::app();
    }
}

namespace quarsintex\quartronic\qcore {

    class QRender extends QSource
    {
        protected $_js;
        protected $_jsFiles;
        protected $_css;
        protected $_cssFiles;
        protected $view;
        protected $_viewDir;
        protected $_sources;

        public $layout = 'layout';
        public $tplExtension = 'php';
        public $content;

        const POSITION_HEAD_BEGIN = 0;
        const POSITION_HEAD_END = 1;
        const POSITION_BODY_BEGIN = 2;
        const POSITION_BODY_END = 3;


        protected function getConnectedParams()
        {
            return [
                'returnRender' => &self::$Q->params['returnRender'],
                'appDir' => &self::$Q->params['appDir'],
                'webDir' => &self::$Q->webDir,
                'webPath' => &self::$Q->webPath,
                'rootDir' => &self::$Q->rootDir,
            ];
        }

        function getViewDir()
        {
            if (!$this->_viewDir) {
                $this->_viewDir = $this->rootDir;
                if ($this->appDir) $this->_viewDir.= $this->appDir.'/';
                $this->_viewDir.= 'qthemes/adminbsb/';
            }
            return $this->_viewDir;
        }

        public function getSources()
        {
            if (!$this->_sources) $this->_sources = new QAssetBundle($this->webDir, $this->webPath);
            return $this->_sources;
        }

        public function getJsList()
        {
            return $this->_js;
        }

        public function getJsFileList()
        {
            return $this->_jsFiles;
        }

        public function getCssList()
        {
            return $this->_css;
        }

        public function getCssFileList()
        {
            return $this->_cssFiles;
        }

        public function registerJs($name, $js, $pos = self::POSITION_BODY_END)
        {
            $this->_js[$pos][$name] = $js;
        }

        public function registerJsFile($path, $pos = self::POSITION_BODY_END)
        {
            $registred = $this->sources->register('js/' . basename($path), $path);
            foreach ($registred as $targetPath => $sourcePath) {
                $this->_jsFiles[$pos][] = $this->sources->assetsPath . $targetPath;
            }
        }

        public function registerCss($name, $css, $pos = self::POSITION_HEAD_END)
        {
            $this->_css[$pos][$name] = $css;
        }

        public function registerCssFile($path, $pos = self::POSITION_HEAD_END)
        {
            $registred = $this->sources->register('css/' . basename($path), $path);
            foreach ($registred as $targetPath => $sourcePath) {
                $this->_cssFiles[$pos][] = $this->sources->assetsPath . $targetPath;
            }
        }

        public function registerDir($sourcePath, $targetPath)
        {
            $this->sources->register($targetPath, $sourcePath, true);
        }

        public function attachResources($pos)
        {
            $output = '';
            if (!empty($this->_cssFiles[$pos])) {
                foreach ($this->_cssFiles[$pos] as $file) {
                    $output .= '<link rel="stylesheet" type="text/css" href="' . $file . '">';
                }
            }

            if (!empty($this->_css[$pos])) {
                $output .= '<style>';
                foreach ($this->_css[$pos] as $code) {
                    $output .= $code;
                }
                $output .= '</style>';
            }

            if (!empty($this->_jsFiles[$pos])) {
                foreach ($this->_jsFiles[$pos] as $file) {
                    $output .= '<script src="' . $file . '"></script>';
                }
            }

            if (!empty($this->_js[$pos])) {
                $output .= '<script>';
                foreach ($this->_js[$pos] as $code) {
                    $output .= $code;
                }
                $output .= '</script>';
            }

            return $output;
        }

        public function run($view, $data = [])
        {
            $this->view = $view;

            foreach ($data as $var => $value) {
                $$var = $value;
            }

            ob_start();
            include($this->viewDir . $view .'.'. $this->tplExtension);
            $this->content = ob_get_clean();

            ob_start();
            include($this->viewDir . $this->layout.'.php');
            $output = ob_get_clean();

            $this->sources->export();

            if ($this->returnRender) {
                return $output;
            } else {
                echo $output;
            }
        }

        public function widget($name, $params = [])
        {
            $className = 'quarsintex\\quartronic\\qwidgets\\' . $name;
            $widget = new $className($params);
            echo $widget->render();
        }

    }
}
?>