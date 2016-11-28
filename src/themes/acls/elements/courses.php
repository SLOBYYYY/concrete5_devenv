<form action="{config:app.shopping_cart_url}" method="post" class="course-form">
	<center>
		<h3>Select Your Courses Below</h3>
	</center>
    <div class="row">
        <div class="col-xs-12 col-md-6 col-lg-3 course-group">
            <div class="course-options exclusive-checkboxes bls-eligible">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="type[]" value="acls_1"> <span class="course-checkbox-name">{training:type:name:acls:1}</span> <span class="course-price">${training:type:cost:acls:1}</span> <span class='includes' data-toggle="popover">(includes)</span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="type[]" value="acls_2"> <span class="course-checkbox-name">{training:type:name:acls:2}</span> <span class="course-price">${training:type:cost:acls:2}</span><span class='includes' data-toggle="popover">(includes)</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="bls-options exclusive-checkboxes" style="display:none">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="type[]" value="bls_1"> <span class="course-checkbox-name">{training:type:name:bls:1}</span> <span class="course-price">Card</span> (no exam required) ${training:type:cost:bls:1}
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="type[]" value="bls_2"> <span class="course-checkbox-name">{training:type:name:bls:2}</span> <span class="course-price">Card</span> (no exam required) ${training:type:cost:bls:2}
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xs-12 col-md-6 col-lg-3 course-group">
            <div class="course-options exclusive-checkboxes bls-eligible">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="type[]" value="pals_1"> <span class="course-checkbox-name">{training:type:name:pals:1}</span> <span class="course-price">${training:type:cost:pals:1}</span> <span class='includes' data-toggle="popover">(includes)</span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="type[]" value="pals_2"> <span class="course-checkbox-name">{training:type:name:pals:2}</span> <span class="course-price">${training:type:cost:pals:2}</span> <span class='includes' data-toggle="popover">(includes)</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="bls-options exclusive-checkboxes" style="display:none">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="type[]" value="bls_1"> <span class="course-checkbox-name">{training:type:name:bls:1}</span> <span class="course-price">Card</span> (no exam required) ${training:type:cost:bls:1}
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="type[]" value="bls_2"> <span class="course-checkbox-name">{training:type:name:bls:2}</span> <span class="course-price">Card</span> (no exam required) ${training:type:cost:bls:2}
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xs-12 col-md-6 col-lg-3 course-group">
            <div class="course-options exclusive-checkboxes bls-eligible">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="type[]" value="nrp_1"> <span class="course-checkbox-name">{training:type:name:nrp:1}</span> <span class="course-price">${training:type:cost:nrp:1}</span> <span class='includes' data-toggle="popover">(includes)</span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="type[]" value="nrp_2"> <span class="course-checkbox-name">{training:type:name:nrp:2}</span> <span class="course-price">${training:type:cost:nrp:2}</span> <span class='includes' data-toggle="popover">(includes)</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="bls-options exclusive-checkboxes" style="display:none">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="type[]" value="bls_1"> <span class="course-checkbox-name">{training:type:name:bls:1}</span> <span class="course-price">Card</span> (no exam required) ${training:type:cost:bls:1}
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="type[]" value="bls_2"> <span class="course-checkbox-name">{training:type:name:bls:2}</span> <span class="course-price">Card</span> (no exam required) ${training:type:cost:bls:2}
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xs-12 col-md-6 col-lg-3 course-group">
            <div class="course-options exclusive-checkboxes bls-options">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="type[]" value="bls_1"> <span class="course-checkbox-name">{training:type:name:bls:1}</span> <span class="course-price">${training:type:cost:bls:1}</span> <span class='includes' data-toggle="popover">(includes)</span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="type[]" value="bls_2"> <span class="course-checkbox-name">{training:type:name:bls:2}</span> <span class="course-price">${training:type:cost:bls:2}</span> <span class='includes' data-toggle="popover">(includes)</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<center>
		<button type="submit" class="btn checkout-button">Register for selected course</button>
	</center>
</form>
