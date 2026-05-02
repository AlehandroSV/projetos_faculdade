document
  .getElementById("vehicleForm")
  .addEventListener("submit", async function (event) {
    event.preventDefault();

    const form = event.target;

    const data = {
      placa: form.placa.value,
      marca: form.marca.value,
      modelo: form.modelo.value,
      ano_fabricacao: form.ano_fabricacao.value,
      ano_modelo: form.ano_modelo.value,
      cor: form.cor.value,
      combustivel: form.combustivel.value,
      quilometragem: form.quilometragem.value,
      chassi: form.chassi.value,
      renavam: form.renavam.value,
      data_cadastro: form.data_cadastro.value,
      observacoes: form.observacoes.value,
    };

    try {
      const response = await fetch("http://localhost:8000/vehicle", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });

      const result = await response.json();

      if (!response.ok) {
        alert(result.error || result.errors?.join("\n"));
        return;
      }

      alert("Veículo cadastrado com sucesso");

      form.reset();
    } catch (error) {
      console.error(error);
      alert("Erro ao cadastrar veículo");
    }
  });
