$(document).ready(function()
{
    candidaciesSelected = [];

    $("#deleteSelected").hide();
    
    $(".checkbox").change(function()
    {
        if(this.checked)
        {
            candidaciesSelected.push(this.id);

            $("#deleteSelected").show();
        }
        else
        {
            const index = candidaciesSelected.indexOf(this.id);
            
            if(index != -1)
            {
                candidaciesSelected.splice(index, 1);

                if(candidaciesSelected == [])
                {
                    $("#deleteSelected").hide();
                }
            }
        }
    });

    $("#deleteSelected").click(function()
    {
        if(confirm("Êtes-vous sûr de vouloir supprimer ces candidatures ?"))
        {
            ids = candidaciesSelected.join();

            //console.log(ids);
            
            $.get("/candidacies/deleteserverals/" + ids, (data, status) =>
            {
                if(status == "success")
                {
                    location.reload();
                }
                else
                {
                    alert("Une erreur s'est produite");
                }
            });
        }
    });
});