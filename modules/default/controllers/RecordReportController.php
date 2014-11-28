<?php

/**
 *  This file is part of SNEP.
 *
 *  SNEP is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  SNEP is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with SNEP.  If not, see <http://www.gnu.org/licenses/>.
 */
include ("includes/functions.php");

/**
 * Record Report Controller
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 */
class RecordReportController extends Zend_Controller_Action {

    /**
     * indexAction
     */
    public function indexAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Reports"),
                    $this->view->translate("Search Records")));

        $form = $this->getForm();

        if ($this->_request->getPost()) {
            $formIsValid = $form->isValid($_POST);

            if ($formIsValid) {
                $this->createAction();
            }
        }

        $this->view->form = $form;
    }

    /**
     * getForm
     * @return \Snep_Form
     */
    private function getForm() {

        $db = Zend_Registry::get('db');

        $form = new Snep_Form();
        $form->setAction($this->getFrontController()->getBaseUrl() . '/record-report/');
        $form->setName('create');

        $form_xml = new Zend_Config_Xml('./modules/default/forms/record_report.xml');

        $dados = new Snep_Form_SubForm($this->view->translate("Records"), $form_xml->form);
        $locale = Snep_Locale::getInstance()->getLocale();

        if ($locale == 'en_US') {
            $now = date('Y-m-d H:i');
            $end = date('Y-m-d') . " 23:59";
        } else {
            $now = date('d/m/Y H:i');
            $end = date('d/m/Y') . " 23:59";
        }

        $initDay = $dados->getElement('init_day');
        $initDay->setValue($now);
        $finalDay = $dados->getElement('end_day');
        $finalDay->setValue($end);

        $form->addSubForm($dados, "dados");

        $srctype = $dados->getElement('srctype');
        $srctype->setValue('src1');

        $dsttype = $dados->getElement('dsttype');
        $dsttype->setValue('dst1');

        $form->getElement('submit')->setLabel($this->view->translate("Show Records"));
        $form->removeElement('cancel');

        return $form;
    }

    /**
     * createAction - Implements data
     */
    public function createAction() {

        $formData = $this->_request->getParams();
        $_SESSION['formDataCRC'] = $formData;

        $config = Zend_Registry::get('config');
        $prefix_inout = $config->ambiente->prefix_inout;
        $dst_exceptions = $config->ambiente->dst_exceptions;

        $date_formated = Snep_RecordReport_Manager::fmtDate($formData['dados']['init_day'], $formData['dados']['end_day']);
        $init = $date_formated['init'];
        $end = $date_formated['end'];

        if (isset($formData['dados']['src'])) {
            $src = $formData['dados']['src'];
        }

        if (isset($formData['dados']['dst'])) {
            $dst = $formData['dados']['dst'];
        }

        if (isset($formData['dados']['srctype'])) {
            $srctype = $formData['dados']['srctype'];
        } else {
            $srctype = "";
        }

        if (isset($formData['dados']['dsttype'])) {
            $dsttype = $formData['dados']['dsttype'];
        } else {
            $dsttype = "";
        }

        $date_clause = " calldate >= '$init'";
        $date_clause .=" AND calldate <= '$end'";
        $condicao = $date_clause;

        // Clausula do where: Origens
        if ($src !== "") {
            if (strpos($src, ",")) {
                $SRC = '';
                $arrSrc = explode(",", $src);
                foreach ($arrSrc as $srcs) {
                    $SRC .= ' OR src LIKE \'' . $srcs . '\' ';
                }
                $SRC = " AND (" . substr($SRC, 3) . ")";
                $condicao .= $SRC;
            } else {
                $condicao = $this->do_field($condicao, $src, substr($srctype, 3), 'src');
            }
        }

        // Clausula do where: Destinos
        if ($dst !== "") {
            if (strpos($dst, ",")) {
                $DST = '';
                $arrDst = explode(",", $dst);
                foreach ($arrDst as $dsts) {
                    $DST .= ' OR dst LIKE \'' . $dsts . '\' ';
                }
                $DST = " AND (" . substr($DST, 3) . ")";
                $condicao .= $DST;
            } else {
                $condicao = $this->do_field($condicao, $dst, substr($dsttype, 3), 'dst');
            }
        }

        /* Clausula do where:  Filtro de desccarte                                    */
        $TMP_COND = "";
        $dst_exceptions = explode(";", $dst_exceptions);
        foreach ($dst_exceptions as $valor) {
            $TMP_COND .= " dst != '$valor' ";
            $TMP_COND .= " AND ";
        }
        $condicao .= " AND ( " . substr($TMP_COND, 0, strlen($TMP_COND) - 4) . " ) ";

        /* Clausula do where: Prefixos de Login/Logout                                */
        if (strlen($prefix_inout) > 3) {
            $COND_PIO = "";
            $array_prefixo = explode(";", $prefix_inout);
            foreach ($array_prefixo as $valor) {
                $par = explode("/", $valor);
                $pio_in = $par[0];
                if (!empty($par[1])) {
                    $pio_out = $par[1];
                }
                $t_pio_in = strlen($pio_in);
                $t_pio_out = strlen($pio_out);
                $COND_PIO .= " substr(dst,1,$t_pio_in) != '$pio_in' ";
                if (!$pio_out == '') {
                    $COND_PIO .= " AND substr(dst,1,$t_pio_out) != '$pio_out' ";
                }
                $COND_PIO .= " AND ";
            }
            if ($COND_PIO != "")
                $condicao .= " AND ( " . substr($COND_PIO, 0, strlen($COND_PIO) - 4) . " ) ";
        }
        $condicao .= " AND ( locate('ZOMBIE',channel) = 0 ) ";


        $dados = Snep_RecordReport_Manager::getCalls($condicao);

        $defaultNS = new Zend_Session_Namespace('call_sql');
        $defaultNS->dados = $dados;

        $this->reportAction();
    }

    /**
     * reportAction - Shows data
     */
    public function reportAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Reports"),
                    $this->view->translate("Record")));

        $format = new Formata;

        $this->view->calldate = $this->view->translate("Call's date");
        $this->view->src = $this->view->translate("Source");
        $this->view->dst = $this->view->translate("Destination");
        $this->view->disposition = $this->view->translate("Status");
        $this->view->duration = $this->view->translate("Duration");
        $this->view->billsec = $this->view->translate("Conversation");
        $this->view->gravation = $this->view->translate("Records");
        $this->view->compress_files = $this->view->translate("Compress selected files");

        $defaultNS = new Zend_Session_Namespace('call_sql');
        $dados = $defaultNS->dados;

        $paginatorAdapter = new Zend_Paginator_Adapter_Array($dados);
        $paginator = new Zend_Paginator($paginatorAdapter);

        $paginator->setCurrentPageNumber($this->_request->page);
        $paginator->setItemCountPerPage(Zend_Registry::get('config')->ambiente->linelimit);

        $items = $paginator->getCurrentItems();

        $this->view->pages = $paginator->getPages();
        $this->view->PAGE_URL = "/snep/index.php/record-report/report/";

        $listItems = array();

        foreach ($items as $item) {

            // Status
            switch ($item['disposition']) {
                case 'ANSWERED':
                    $item['disposition'] = $this->view->translate('Answered');
                    break;
                case 'NO ANSWER':
                    $item['disposition'] = $this->view->translate('Not Answered');
                    break;
                case 'FAILED':
                    $item['disposition'] = $this->view->translate('Failed');
                    break;
                case 'BUSY':
                    $item['disposition'] = $this->view->translate('Busy');
                    break;
                case 'OTHER':
                    $item['disposition'] = $this->view->translate('Others');
                    break;
            }

            $item['src'] = $format->fmt_telefone(array("a" => $item['src']));
            $item['dst'] = $format->fmt_telefone(array("a" => $item['dst']));
            $item['billsec'] = $format->fmt_segundos(array("a" => $item['billsec'], "b" => 'hms'));
            $item['duration'] = $format->fmt_segundos(array("a" => $item['duration'], "b" => 'hms'));

            $filePath = Snep_Manutencao::arquivoExiste($item['calldate'], $item['userfield']);
            $item['file_name'] = $item['userfield'] . ".wav";

            if ($filePath) {
                $item['file_path'] = $filePath;
                $item['file'] = true;
            } else {
                $item['file_path'] = 'N.D.';
                $item['file'] = false;
            }

            array_push($listItems, $item);
        }

        if (empty($listItems)) {
            $this->view->error = $this->view->translate("No entries found!.");
            $this->_helper->viewRenderer('error');
        } else {
            $this->view->dados = $listItems;
            $this->view->compact_success = $this->view->translate("The files were compressed successfully! Wait for the download start.");
            $this->renderScript('record-report/report.phtml');
        }
    }

    /**
     * do_field - implements condition
     * @param <string> $sql
     * @param <string> $fld
     * @param <string> $fldtype
     * @param <string> $nmfld
     * @param <string> $tpcomp
     * @return <string>
     */
    public function do_field($sql, $fld, $fldtype, $nmfld = "", $tpcomp = "AND") {
        if (isset($fld) && ($fld != '')) {
            $sql = "$sql $tpcomp";

            if ($nmfld == "") {
                $sql = "$sql $fld";
            } else {
                $sql = "$sql $nmfld";
            }

            if (isset($fldtype)) {
                switch ($fldtype) {
                    case 1:
                        $sql = "$sql='" . $fld . "'";
                        break;
                    case 2:
                        $sql = "$sql LIKE '" . $fld . "%'";
                        break;
                    case 3:
                        $sql = "$sql LIKE '%" . $fld . "'";
                        break;
                    case 4:
                        $sql = "$sql LIKE '%" . $fld . "%'";
                        break;
                }
            } else {
                $sql = "$sql LIKE '%" . $fld . "%'";
            }
        }
        return $sql;
    }

    /**
     * compactAction - Compact archives
     */
    public function compactAction() {

        $config = Zend_Registry::get('config');
        $this->_helper->layout->disableLayout();
        $path = $config->ambiente->path_voz;

        $zip = new ZipArchive();
        $files = $this->_request->getParam('files');
        $fileName = date("d-m-Y-h-i") . ".zip";

        $caminho = explode("snep", $path);
        $caminho = $caminho[0];

        $zip->open($path . $fileName, ZipArchive::CREATE);
        $arrFiles = explode(',', $files);

        foreach ($arrFiles as $file) {

            $file = $caminho . $file;
            $zip->addFile($file, $file);
        }
        $zip->close();
        $this->view->path = '/snep/arquivos/' . $fileName;
    }

    /**
     * errorAction
     */
    public function errorAction() {
        
    }

}
