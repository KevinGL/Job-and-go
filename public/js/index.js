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

                if(candidaciesSelected.length == 0)
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

    $("#select_all").change(function()
    {
        candidaciesSelected = [];
        
        for(let i=0; i<$(".checkbox").length; i++)
        {
            $(".checkbox")[i].checked = this.checked;

            if(this.checked)
            {
                candidaciesSelected.push($(".checkbox")[i].id);
            }
            else
            {
                candidaciesSelected = [];
            }
        }

        if(this.checked)
        {
            $("#deleteSelected").show();
        }
        else
        {
            $("#deleteSelected").hide();
        }
    });

    $("#validMonth").click(() =>
    {
        if($("#month").val() != "")
        {
            document.location.href = "/candidacies/graph_by_date/" + $("#month").val();
        }
    });
});