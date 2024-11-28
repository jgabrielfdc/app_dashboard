<?php


//classe dashboard
class Dashboard implements JsonSerializable{
    private $data_inicio;
    private $data_fim;
    private $numeroVendas;
    private $totalVendas;
    private $clientesAtivos;
    private $clientesInativos;
    private $totalReclamacoes;
    private $totalElogios;
    private $totalSugestoes;
    private $totalDespesas;

    public function __get($atributo) {
		return $this->$atributo;
	}

    public function __set($atributo, $valor) {
		$this->$atributo = $valor;
		return $this;
	}

    public function jsonSerialize():array
    {
        return [
        'data_inicio'=>$this->data_inicio,
        'data_fim'=>$this->data_fim,
        'numeroVendas'=>$this->numeroVendas,
        'totalVendas'=>$this->totalVendas,
        'clientesAtivos'=>$this->clientesAtivos,
        'clientesInativos'=>$this->clientesInativos,
        'totalReclamacoes'=>$this->totalReclamacoes,
        'totalElogios'=>$this->totalElogios,
        'totalSugestoes'=>$this->totalSugestoes,
        'totalDespesas'=>$this->totalDespesas,
        ];
    }
}

//Classe conexao bd

class Conexao{
    private $host="localhost"; 
    private $dbname="dashboard";
    private $user="root";
    private $password="";

    public function conectar(){
        try {
            $conexao = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname",
                "$this->user",
                "$this->password"
            );
 
            $conexao->exec('set charset utf8');
 
            return $conexao;
        }catch(PDOException $e){
            echo "<p>".$e->getMessage()."</p>";
        }
        
    }
}

//class (model)
class Bd{
    private $conexao;
    private $dashboard;

    public function __construct(Conexao $conexao, Dashboard $dashboard) {
		$this->conexao = $conexao->conectar();
		$this->dashboard = $dashboard;
	}  

    public function getNumeroVendas(){
        $query="SELECT count(*) as numero_vendas FROM tb_vendas WHERE data_venda BETWEEN :data_inicio AND :data_fim";

        $stmt=$this->conexao->prepare($query);
        $stmt->bindValue(":data_inicio",$this->dashboard->data_inicio);
        $stmt->bindValue(":data_fim",$this->dashboard->data_fim);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
    }

    public function getTotalVendas(){
        $query="SELECT sum(total) as total_vendas FROM tb_vendas WHERE data_venda BETWEEN :data_inicio AND :data_fim";

        $stmt=$this->conexao->prepare($query);
        $stmt->bindValue(":data_inicio",$this->dashboard->data_inicio);
        $stmt->bindValue(":data_fim",$this->dashboard->data_fim);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
    }
    public function getClientesAtivos(){
        $query="SELECT count(cliente_ativo) as total_ativos FROM tb_clientes WHERE cliente_ativo=1";

        $stmt=$this->conexao->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ)->total_ativos;
    }
    public function getClientesInativos(){
        $query="SELECT count(cliente_ativo) as total_inativos FROM tb_clientes WHERE cliente_ativo=0";

        $stmt=$this->conexao->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ)->total_inativos;
    }
    public function getTotalDespesas(){
        $query="SELECT sum(total) as total_despesas FROM tb_despesas WHERE data_despesa BETWEEN :data_inicio AND :data_fim";

        $stmt=$this->conexao->prepare($query);
        $stmt->bindValue(":data_inicio",$this->dashboard->data_inicio);
        $stmt->bindValue(":data_fim",$this->dashboard->data_fim);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ)->total_despesas;
    }

    public function getTotalElogios(){
        $query="SELECT sum(tipo_contato) as total_elogios FROM tb_contatos WHERE tipo_contato=3";

        $stmt=$this->conexao->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ)->total_elogios;
    }
    public function getTotalSugestoes(){
        $query="SELECT sum(tipo_contato) as total_sugestoes FROM tb_contatos WHERE tipo_contato=2";

        $stmt=$this->conexao->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ)->total_sugestoes;
    }
    public function getTotalReclamacoes(){
        $query="SELECT sum(tipo_contato) as total_reclamacoes FROM tb_contatos WHERE tipo_contato=1";

        $stmt=$this->conexao->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ)->total_reclamacoes;
    }
}

$dashboard=new Dashboard();

$competencia="2018-10";
$competencia=explode("-",$_GET['competencia']);

$ano=$competencia[0];
$mes=$competencia[1];
$dias_do_mes=cal_days_in_month(CAL_GREGORIAN,$mes,$ano);

$data_inicio=$ano."-".$mes."-01";
$data_fim=$ano."-".$mes."-".$dias_do_mes;

$dashboard->__set('data_inicio', $data_inicio);
$dashboard->__set('data_fim', $data_fim);

$conexao=new Conexao();

$bd = new Bd($conexao, $dashboard);

$dashboard->__set("numeroVendas",$bd->getNumeroVendas());
$dashboard->__set("totalVendas",$bd->getTotalVendas());
$dashboard->__set("clientesAtivos",$bd->getClientesAtivos());
$dashboard->__set("clientesInativos",$bd->getClientesInativos());
$dashboard->__set("totalElogios",$bd->getTotalElogios());
$dashboard->__set("totalSugestoes",$bd->getTotalSugestoes());
$dashboard->__set("totalReclamacoes",$bd->getTotalReclamacoes());

$dashboard->__set("totalDespesas",$bd->getTotalDespesas());


echo json_encode($dashboard);
?>