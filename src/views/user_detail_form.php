<form action="" id="frmUserDetail">


    <div class="row">
        <div class="col-md-6">
            <label for="FirstName">First Name *</label>
            <br>
            <input type="text" name="first_name" required>
        </div>
        <div class="col-md-6">
            <label for="LastName">Last Name *</label>
            <br>
            <input type="text" name="last_name" required>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <label for="PhoneNumber">Phone Number</label>
            <br>
            <input type="text" name="phone_number">
        </div>
        <div class="col-md-6">
            <label for="Email">Email *</label>
            <br>
            <input type="email" name="email" required>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <label for="Address">Address</label>
            <br>
            <input type="text" name="address" id="address">
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <label for="Username">Username *</label>
            <br>
            <input type="text" name="username" id="txtUsername" required>
        </div>
        <div class="col-md-6">
            <label for="Password">Password *</label>
            <br>
            <input type="password" name="password" required>
        </div>
    </div>

    <br><br>

    <h3>Enter your vehicle details</h3>

    <br>

    <div class="row">
        <div class="col-md-6">
            <label for="LLCName">LLC Name if Applicable (Private)</label>
            <br>
            <input type="text" name="llc_name">
        </div>
        <div class="col-md-6">
            <label for="Vehicle">Vehicle</label>
            <br>
            <input type="text" name="vehicle">
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <label for="Year">Year</label>
            <br>
            <input type="text" name="year">
        </div>
        <div class="col-md-6">
            <label for="EngineType">Engine Type</label>
            <br>
            <select name="engine_type" id="engine_type">
                <option value="Gas">Gas</option>
                <option value="Diesel">Diesel</option>
                <option value="Electric">Electric</option>
                <option value="Hybrid">Hybrid</option>
            </select>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <label for="VIN">VIN</label>
            <br>
            <input type="text" name="vin">
        </div>
        <div class="col-md-6">
            <label for="Color">Color</label>
            <br>
            <input type="text" name="color">
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <label for="Bank Name">Miles</label>
            <br>
            <input type="text" name="miles">
        </div>
        <div class="col-md-6">
            <label>Are you paying cash for the vehicle?</label>
            <br>
            <input type="radio" value="1" name="paying_cash" onclick="jQuery('#bank_details_wrapper').hide();"> Yes 
            <input type="radio" value="0" name="paying_cash" onclick="jQuery('#bank_details_wrapper').show();"> No
        </div>
    </div>

    <br>

    <div id="bank_details_wrapper">
        <div class="row">
            <div class="col-md-6">
                <label for="Bank Name">Bank Name</label>
                <br>
                <input type="text" name="bank_name">
            </div>
            <div class="col-md-6">
                <label for="Bank Name">Bank Address</label>
                <br>
                <input type="text" name="bank_address">
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-6">
                <label for="Bank Name">Bank Phone</label>
                <br>
                <input type="text" name="bank_phone">
            </div>
            <div class="col-md-6">
                <label for="Bank Name">Bank Contact</label>
                <br>
                <input type="text" name="bank_contact">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label for="Bank Name">Do you have turn signals already to make your UTV street legal?</label>
            <br>
            <input type="radio" value="1" name="have_turn_signal" > Yes 
            <input type="radio" value="0" name="have_turn_signal" > No
        </div>
        <div class="col-md-6"></div>
    </div>

<input type="hidden" id="txtVehicleType" name="vehicle_type">
<input type="hidden" id="txtState" name="state">

<div id="formErrors">
    <div class="alert alert-danger"></div>
</div>

<div id="formData"></div>

</form>

<script>
    
    
document.addEventListener("DOMContentLoaded", function () {
    function initAutocomplete() {
        var input = document.getElementById("address");
        var autocomplete = new google.maps.places.Autocomplete(input);

        // Optionally, restrict results to a specific country
        autocomplete.setComponentRestrictions({ 'country': ['us'] });

        // Listen for address selection
        autocomplete.addListener("place_changed", function () {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                console.log("No details available for input: '" + input.value + "'");
                return;
            }
            console.log("Selected address:", place.formatted_address);
        });
    }

    initAutocomplete();
});

    // hideSteps();
    

</script>