class DataMapaDespac {
    constructor(apiBaseUrl,cod_servic,num_despac) {
      this.apiBaseUrl = apiBaseUrl; 
      this.cod_servic = cod_servic;
      this.num_despac = num_despac;
    }
  
    async getData() {
      
        

        let cod_servic = this.cod_servic;
        let paramsJson = {  
            "option":"dataMapaDespac",
            "num_despac":this.num_despac,
        };

        const url = `${this.apiBaseUrl}`;

        let formaData = new FormData();

        formaData.append('paramsJson', JSON.stringify(paramsJson));
        formaData.append('cod_servic', cod_servic);
        
        return fetch(url, {
            method: "POST",
            body: formaData
        })
        .then(response => response.text())
        .then(data => {

            /*if(data.ind_estado!=1){
                $("#mapaID").empty();
            }*/
            $('#paintMapID').html(data);
        })
        .catch(err => {
            console.log(err);
            
        });

    }

    // Mostrar y ocultar el indicador de carga (usando jQuery blockUI)
    loadAjax(action) {
        try {
            if (action === "start") {
                //$.blockUI({ message: '<div>Espere un momento</div>' });
            } else {
                //$.unblockUI();
            }
        } catch (error) {
            console.log(error);
        }
    }
}

        
  


