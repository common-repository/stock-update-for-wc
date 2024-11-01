$ = jQuery;
$(document).ready(function () {
    jQuery(".using_file").hide();

    jQuery(".paone_su_checkbox").click(function () {
        jQuery(".using_file").toggle();
        jQuery(".using_post_fields").toggle();
    });
    validateStockUpdateForm();

})

function setTextColor(picker) {
    document.getElementsByTagName('body')[0].style.color = '#' + picker.toString()
}
function validateStockUpdateForm() {
    $("#paone_su_admin_setting").validate({
        // ignore: [],
        rules: {
            "stocks[Sku][]": "required",
            "stocks[Stock][]": {required:true,number: true}
        },
        messages: {
            // name: "Please enter the Printed Name",
            // fonts: "Please enter available font names",
            // colors: "Please enter available font colors",
            // max_characters: "Please enter number of characters allowed to be printed.",
        },
        checkForm: function() {
            this.prepareForm();
            for ( var i = 0, elements = (this.currentElements = this.elements()); elements[i]; i++ ) {
                if (this.findByName( elements[i].name ).length != undefined && this.findByName( elements[i].name ).length > 1) {
                    for (var cnt = 0; cnt < this.findByName( elements[i].name ).length; cnt++) {
                        this.check( this.findByName( elements[i].name )[cnt] );
                    }
                } else {
                    this.check( elements[i] );
                }
            }
            return this.valid();
        }
    });
}