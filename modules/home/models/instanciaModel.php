<?php
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');

class instanciaModel extends ModularCore\Model{

	function __construct() {
		parent::__construct();
		//error_reporting(0);
		$this->loadDb('oraclePRD', 'oracle');
	}

	function getInstancia($instancia){
		$this->db->oracle->sqlBuilder->select('*');
		$this->db->oracle->sqlBuilder->from('WFPROCESS P');
		$this->db->oracle->sqlBuilder->where("P.idprocess = '$instancia'");
		return $this->db->oracle->sqlBuilder->executeQuery();
	}
	
	function getAtividadesPendentesAoMeuUsuario($ip){
		$usuario = $this->getUserSE($ip);
		$this->db->oracle->sqlBuilder->select('distinct p.idprocess, a.NMSTRUCT, a.idstruct, a.dtenabled, a.tmenabled, a.dtestimatedfinish, a.NRTIMEESTFINISH, p.NMUSERSTART');
		$this->db->oracle->sqlBuilder->from('wfprocess p');
		$this->db->oracle->sqlBuilder->join("wfstruct a on a.idprocess = p.idobject and a.fgstatus = 2");
		$this->db->oracle->sqlBuilder->join("wfactivity b on a.idobject = b.idobject");
		$this->db->oracle->sqlBuilder->leftjoin("aduserrole on aduserrole.cdrole = b.cdrole");		
		$this->db->oracle->sqlBuilder->where("p.fgstatus = 1  and (aduserrole.cduser = 9 or b.cduser = 9)");
		$this->db->oracle->sqlBuilder->orderby("a.dtestimatedfinish, a.NRTIMEESTFINISH, p.idprocess");
		return $this->db->oracle->sqlBuilder->executeQuery();
	}
	
	function getPapeis($ip){
		$usuario = $this->getUserSE($ip);
		$this->db->oracle->sqlBuilder->select('*');
		$this->db->oracle->sqlBuilder->from('aduserrole role');
		$this->db->oracle->sqlBuilder->where("role.cduser = $usuario");

		return $this->db->oracle->sqlBuilder->executeQuery();
	}
	function getEquipes($ip){
		$usuario = $this->getUserSE($ip);
		$this->db->oracle->sqlBuilder->select('*');
		$this->db->oracle->sqlBuilder->from('ADTEAMMEMBER role');
		$this->db->oracle->sqlBuilder->where("role.cduser = $usuario");

		return $this->db->oracle->sqlBuilder->executeQuery();
	}

	function getInstanciaExecucao($instancia, $ip){

		$usuario = $this->getUserSE($ip);


		$this->db->oracle->sqlBuilder->select('p.idobject AS IDPROCESS, c.idobject');
		$this->db->oracle->sqlBuilder->from('WFPROCESS P');
		$this->db->oracle->sqlBuilder->join('WFSTRUCT A ON A.IDPROCESS = P.IDOBJECT');
		$this->db->oracle->sqlBuilder->join('WFACTIVITY B ON B.IDOBJECT = A.IDOBJECT');
		$this->db->oracle->sqlBuilder->join('wftask c on c.IDACTIVITY = b.IDOBJECT');
		$this->db->oracle->sqlBuilder->where("A.FGSTATUS = 2 AND P.idprocess = '$instancia' and B.cduser = '$usuario'");
		$return = $this->db->oracle->sqlBuilder->executeQuery();
		if ($return) {
			return $return[0];
		}
		return false;
	}

	private function getUserSE($ip){
		$this->db->oracle->sqlBuilder->select('U.cduser');
		$this->db->oracle->sqlBuilder->FROM('SEUSERSESSION S');
		$this->db->oracle->sqlBuilder->JOIN('ADUSER U ON U.IDLOGIN = S.IDLOGIN');
		$this->db->oracle->sqlBuilder->WHERE("FGSESSIONTYPE = 2 AND
												to_char(DTDATE, 'DD/MM/YY') = to_char(sysdate, 'DD/MM/YY') AND
												nmloginaddress = '$ip'");
		$this->db->oracle->sqlBuilder->orderby('DTDATE desc');
		$return = $this->db->oracle->sqlBuilder->executeQuery();
		//var_dump($this->db->oracle->sqlBuilder->query()); die();

		//echo $this->db->oracle->sqlBuilder->getQuery(); die();
		if(isset($return[0]))
			return $return[0]['CDUSER'];
		else
			return false;
	}

	function getUserSEMatricula($ip){
		$this->db->oracle->sqlBuilder->select('U.IDUSER');
		$this->db->oracle->sqlBuilder->FROM('SEUSERSESSION S');
		$this->db->oracle->sqlBuilder->JOIN('ADUSER U ON U.IDLOGIN = S.IDLOGIN');
		$this->db->oracle->sqlBuilder->WHERE("FGSESSIONTYPE = 2 AND
												to_char(DTDATE, 'DD/MM/YY') = to_char(sysdate, 'DD/MM/YY') AND
												nmloginaddress = '$ip'");
		$this->db->oracle->sqlBuilder->orderby('DTDATE desc');
		$return = $this->db->oracle->sqlBuilder->executeQuery();
		//var_dump($this->db->oracle->sqlBuilder->query()); die();

		//echo $this->db->oracle->sqlBuilder->getQuery(); die();
		if(isset($return[0]))
			return $return[0]['IDUSER'];
		else
			return false;
	}
	
	function getTokenFromSAP($recurso = 'workflow_manutencao_romaneio'){
		if($this->db->oracle->sqlBuilder->db == 'oraclePRD'){
			$this->db->oracle->sqlBuilder->select("SUBSTR(t.resultado, 1, INSTR( t.resultado, '|' )-1 ) as URI, SUBSTR(t.resultado, INSTR( t.resultado, '|' ) + 1, LENGTH(t.resultado)) as token");
			$this->db->oracle->sqlBuilder->from("(Select sap_xi.pkg_webservices_sap.fnc_retorna_param_webservices2@SEPRD(precurso => '$recurso',  psistema => 'SE', pAmbiente => 'PRD') resultado from dual ) T");
		}else{ //caso seja QAS
			$this->db->oracle->sqlBuilder->select("SUBSTR(t.resultado, 1, INSTR( t.resultado, '|' )-1 ) as URI, SUBSTR(t.resultado, INSTR( t.resultado, '|' ) + 1, LENGTH(t.resultado)) as token");
		$this->db->oracle->sqlBuilder->from("(Select sap_xi.pkg_webservices_sap.fnc_retorna_param_webservices2(precurso => '$recurso',  psistema => 'SE', pAmbiente => 'QAS') resultado from dual ) T");
		}
		return $this->db->oracle->sqlBuilder->executeQuery()[0];

	}


	function getStatusPedido($nrPedido){
		$this->db->oracle->sqlBuilder->select("NR_PEDIDO,NM_FORNECEDOR,DS_STATUS");
		$this->db->oracle->sqlBuilder->from("SAP_XI.VW_CONTRATO_EXPRESSO_WS@SEPRD ");
		$this->db->oracle->sqlBuilder->where("NR_PEDIDO = '$nrPedido' AND ROWNUM < = 1");
		return $this->db->oracle->sqlBuilder->executeQuery()[0];
	}

	function getStatusRequisicao($nrRequisicao){
		$this->db->oracle->sqlBuilder->select("STATUS_LIB as DS_STATUS");
		$this->db->oracle->sqlBuilder->from("SAP_XI.VW_REQUISICAO_WS");
		$this->db->oracle->sqlBuilder->where("NUM_REQ = '$nrRequisicao' AND ROWNUM < = 1");
		return $this->db->oracle->sqlBuilder->executeQuery()[0];
	}



	



}



