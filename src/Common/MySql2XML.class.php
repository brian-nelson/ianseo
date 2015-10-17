<?php
/**
 * MySql2XML
 * Motore per convertire un recordset MySql in un documento xml.
 *
 * La classe viene istanziata con 1,2 o 3 parametri.
 * Con 1 parametro:
 *   viene generato all'interno dell'oggetto un documento xml e viene creata la root 'response'.
 *   Questo nodo diventa il master node a cui il motore aggiungerà i dati.
 * Con 2 parametri:
 *   il secondo parametro è il documento che non deve avere una root definita.
 *   Questa verrà creata e sarà 'response' che come nel caso precedente diventerà il master node.
 * Con 3 parametri:
 *   l'ultimo parametro è la root del documento è verrà usata come master node.
 *
 * Alla fine del processo di generazione nel parametro $xmlDoc sarà presente il documento xml e in $xmlMasterNode il master node.
 * Nel caso però questi parametri siano null, il documento e il master node possono essere ottenuti tramite
 * i metodi getXmlDoc e getXmlMasterNode.
 *
 * @author Guerra Simone
 * @version 20081029.2
 *
 */

//TODO Aggiungere gestione delle eccezioni
	class MySql2XML
	{
	/**
	 * Documento xml.
	 * Se il costruttore ha null come parametro allora viene creato un nuovo documento
	 *
	 * @var DOMDocument
	 * @access private
	 */
		private $xmlDoc=null;

	/**
	 * Nodo a cui appendere i dati estratti.
	 * Se il costruttore riceve null come parametro viene usato response (come root)
	 *
	 * @var DOMNode
	 * @access private
	 */
		private $xmlMasterNode=null;

	/**
	 * Recordset dei dati
	 *
	 * @var Result Set dal Database
	 * @access: private
	 */
		private $rs=null;

	/**
	 * Costruttore
	 *
	 * @param $rs: recordset dei dati
	 * @param DOMDocument $xmlDoc: documento xml
	 * @param DOMNode $xmlMasterNode: Master node del documento
	 *
	 * $rs deve essere avere i campi con i nomi nalla forma `nodo_padre->...nodo_padre->nodo` oppure
	 * `nodo` oppure `__nome`; i campi nell'ultima forma verranno ignorati.
	 * Ogni nodo_padre rappresenta la radice di un sottoalbero, il primo è agganciato al masternode.
	 *
	 * (Per la gestione di $xmlDoc e $xmlMasterNode vedi la descrizione della classe).
	 */
		public function __construct($rs,$xmlDoc=null,$xmlMasterNode=null)
		{
			$this->rs=$rs;

		// var temporanee
			$tmpDoc=$xmlDoc;
			$tmpMaster=$xmlMasterNode;

		// se il secondo parametro è null creo il documento
			if (is_null($xmlDoc))
			{
				$tmpDoc=new DOMDocument('1.0','UTF-8');
			}

		// se il terzo parametro è null creo la root 'response' e l'appendo
			if (is_null($xmlMasterNode))
			{
				$tmpMaster=$tmpDoc->createElement('response');
				$tmpDoc->appendChild($tmpMaster);
			}

		// inizializzo le var private di appoggio
			$this->xmlDoc=$tmpDoc;
			$this->xmlMasterNode=$tmpMaster;

		// genero l'export
			$this->make();
		}

	/**
	 * Produce i dati xml e li aggancia al master node
	 *
	 * Il recordset viene letto dall'alto e da sinistra.
	 * Quando un campo presenta la definzione di un nodo, se questo non c'è viene creato e aggiunto
	 * al master node.
	 */
		private function make()
		{
			if ($this->rs)
			{
				while ($myRow=safe_fetch($this->rs))
				{
				// array dei nodi xml creati
					$createdNodes=array();

				/**
				 * Per ogni colonna verifico come stanno i nodi in $nodes
				 */
					foreach ($myRow as $Key=>$Value)
					{
					// escludo i nomi che iniziano con __
						if (substr($Key,0,2)=='__')
							continue;

						$name=$Key;	// nome nella forma nodo_padre->...nodo_padre->nodo oppure solo nodo

						$nodes=explode('->',$name);

					/**
					 * Per ogni elemento, tranne l'ultimo, verifico se ho già creato il nodo xml.
					 * Se non c'è, lo creo e lo aggancio
					 */
						$j=0;
						//print '..' .count($nodes) . '<br>';
						for (;$j<count($nodes)-1;++$j)
						{
							//print '[' . $j . '] ';

						// Se non trovo nodo:$j lo creo e lo appendo
							if (array_key_exists($nodes[$j] . ':' . $j,$createdNodes)===false)
							{
							// creo
								$createdNodes[$nodes[$j] . ':' . $j]=$this->xmlDoc->createElement($nodes[$j]);

							// appendo...
								if ($j==0)	// ...al masternode
								{
									$this->xmlMasterNode->appendChild($createdNodes[$nodes[$j] . ':' . $j]);
								}
								else	//..al padre
								{
									$createdNodes[$nodes[$j-1] . ':' . ($j-1)]->appendChild($createdNodes[$nodes[$j] . ':' . $j]);
								}
							}
						}
					// creo il nodo informazione
						$tmp=$this->xmlDoc->createElement($nodes[$j],$Value);

					/*
					 * Avevo un nome nella forma nodo_padre->...nodo_padre->nodo quindi
					 * il nodo info è in un sottoalbero
					 */
						if (count($nodes)>1)
						{
							$createdNodes[$nodes[$j-1] . ':' . ($j-1)]->appendChild($tmp);
						}
					// avevo un nome nella forma nodo quindi attacco al MasterNode
						else
						{
							$this->xmlMasterNode->appendChild($tmp);
						}

					}
				}
			}
		}

	/**
	 * Ritorna il documento xml creato.
	 * Ha senso usare questo metodo se l'oggetto è stato istanziato con il secondo parametro null
	 *
	 * @return DOMDocument
	 */
		public function getXmlDoc()
		{
			return $this->xmlDoc;
		}

	/**
	 * Ritorna il MasterNode del documento.
	 * Ha senso usare questo metodo se l'oggetto è stato istanziato con il terzo parametro null
	 *
	 * @return DOMNode
	 */
		public function getXmlMasterNode()
		{
			return $this->xmlMasterNode;
		}
	}
?>