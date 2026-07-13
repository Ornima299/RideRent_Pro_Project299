document.addEventListener("DOMContentLoaded", function () {

    const form = document.querySelector("form");

    form.addEventListener("submit", function (e) {

        let errors = [];

        //==========================
        // Get Form Values
        //==========================

        const vehicleName = document.getElementById("vehicle_name").value.trim();
        const brand = document.getElementById("brand").value.trim();
        const model = document.getElementById("model").value.trim();
        const year = parseInt(document.getElementById("year").value);
        const vehicleType = document.getElementById("vehicle_type").value;
        const fuelType = document.getElementById("fuel_type").value;
        const transmission = document.getElementById("transmission").value;
        const seats = parseInt(document.getElementById("seat_capacity").value);
        const price = parseFloat(document.getElementById("price_per_day").value);
        const location = document.getElementById("location").value.trim();
        const availability = document.getElementById("availability").value;
        const description = document.getElementById("description").value.trim();

        const imageInput = document.getElementById("image");

        //==========================
        // Required Fields
        //==========================

        if(vehicleName==="")
            errors.push("Vehicle Name is required.");

        if(brand==="")
            errors.push("Brand is required.");

        if(model==="")
            errors.push("Model is required.");

        if(isNaN(year))
            errors.push("Year is required.");

        if(vehicleType==="")
            errors.push("Vehicle Type is required.");

        if(fuelType==="")
            errors.push("Fuel Type is required.");

        if(transmission==="")
            errors.push("Transmission is required.");

        if(isNaN(seats))
            errors.push("Seat Capacity is required.");

        if(isNaN(price))
            errors.push("Price Per Day is required.");

        if(location==="")
            errors.push("Location is required.");

        if(description==="")
            errors.push("Description is required.");

        //==========================
        // Price Validation
        //==========================

        if(!isNaN(price) && price<=0){

            errors.push("Price must be greater than 0.");

        }

        //==========================
        // Year Validation
        //==========================

        if(!isNaN(year)){

            if(year<2015 || year>2026){

                errors.push("Year must be between 2015 and 2026.");

            }

        }

        //==========================
        // Seat Validation
        //==========================

        if(!isNaN(seats)){

            if(seats<=0){

                errors.push("Seat Capacity must be greater than 0.");

            }

        }

        //==========================
        // Description Length
        //==========================

        if(description.length>500){

            errors.push("Description cannot exceed 500 characters.");

        }

        //==========================
        // Image Validation
        //==========================

        if(imageInput.files.length===0){

            errors.push("Vehicle Image is required.");

        }else{

            const file=imageInput.files[0];

            const extension=file.name.split('.').pop().toLowerCase();

            const allowed=["jpg","jpeg","png"];

            if(!allowed.includes(extension)){

                errors.push("Only JPG, JPEG and PNG images are allowed.");

            }

            const maxSize=5*1024*1024;

            if(file.size>maxSize){

                errors.push("Image size must be less than 5 MB.");

            }

        }

        //==========================
        // Show Errors
        //==========================

        if(errors.length>0){

            e.preventDefault();

            alert(errors.join("\n"));

        }

    });

});