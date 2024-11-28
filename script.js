$(document).ready(() => {
    $('#documentacao').on("click",() => {
		$("#pagina").load("documentacao.html");
	})

	$('#suporte').on("click",() => {
		$("#pagina").load("suporte.html");
	})


	// Ajax

	$("#competencia").on("change", e=>{
		let competencia=$(e.target).val()
		console.log(competencia)

		$.ajax({
			type:"GET",
			data:"competencia="+competencia,
			url:"app.php",
			success:(dados=>{
				let dadosJS=JSON.parse(dados);
				$("#numeroVendas").html(dadosJS.numeroVendas);
				$("#totalVendas").html(dadosJS.totalVendas);
				$("#clientesAtivos").html(dadosJS.clientesAtivos);
				$("#clientesInativos").html(dadosJS.clientesInativos);
				$("#totalReclamacoes").html(dadosJS.totalReclamacoes);
				$("#totalSugestoes").html(dadosJS.totalSugestoes);
				$("#totalElogios").html(dadosJS.totalElogios);
				$("#totalDespesas").html(dadosJS.totalDespesas);
			}),
			error:erro=>{
				console.log(erro)
			}
			
		})
	})
})
