function confirmDelete(id){

let result = confirm("Are you sure you want to delete this vehicle?");

if(result){

window.location="delete_vehicle.php?id="+id;

}

}
