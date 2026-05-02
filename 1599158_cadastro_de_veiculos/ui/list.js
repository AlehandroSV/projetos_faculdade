document.addEventListener("DOMContentLoaded", loadVehicles);

async function loadVehicles() {
  try {
    const response = await fetch("http://localhost:8000/vehicle");
    const vehicles = await response.json();

    const tbody = document.getElementById("vehiclesBody");
    tbody.innerHTML = "";

    vehicles.forEach((vehicle) => {
      const row = document.createElement("tr");

      row.innerHTML = `
                <td>${vehicle.id}</td>
                <td>${vehicle.placa}</td>
                <td>${vehicle.marca}</td>
                <td>${vehicle.modelo}</td>
                <td>${vehicle.ano_fabricacao}</td>
                <td>${vehicle.ano_modelo}</td>
                <td>${vehicle.cor}</td>
                <td>${vehicle.combustivel}</td>
                <td>${vehicle.quilometragem}</td>
                <td>${vehicle.chassi}</td>
                <td>${vehicle.renavam}</td>
                <td>${vehicle.data_cadastro}</td>
                <td>${vehicle.observacoes ?? ""}</td>
            `;

      tbody.appendChild(row);
    });
  } catch (error) {
    console.error("Erro ao carregar veículos", error);
  }
}
