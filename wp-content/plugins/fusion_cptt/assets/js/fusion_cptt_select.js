/* Show dynamicaly taxonomies and terms */
(function ($) {

    $(document).ready(function () {

        function updateSelectorsOnChange() {
            //remove previous terms selection
            var cus_term = $('#cus_terms');
            cus_term.find('option:selected').removeAttr('selected');
            cus_term.trigger("chosen:updated");

            var cus_terms_exclude = $('#cus_terms_exclude');
            cus_terms_exclude.find('option:selected').removeAttr('selected');
            cus_terms_exclude.trigger("chosen:updated");

            //get the value of selected post type option
            var cus_post_type_selected = $('#cpt_post_type').find('option:selected').val();

            //hide all taxonomy elements
            var cus_taxonomy = $('#cus_taxonomy');
            cus_taxonomy.find('option[value]').attr('disabled', 'disabled').hide();

            var meta_terms1 = $('#meta_terms1');
            var meta_terms2 = $('#meta_terms2');
            meta_terms1.find('option[value]').attr('disabled', 'disabled').hide();
            meta_terms2.find('option[value]').attr('disabled', 'disabled').hide();

            //show taxonomies that start with post type selected
            cus_taxonomy.find('option[value="xxx__select_taxonomy"]').removeAttr('disabled').show();
            meta_terms1.find('option[value="xxx__select_taxonomy"]').removeAttr('disabled').show();
            meta_terms2.find('option[value="xxx__select_taxonomy"]').removeAttr('disabled').show();

            cus_taxonomy.find('option[value^=' + cus_post_type_selected + '__]').removeAttr('disabled').show();
            meta_terms1.find('option[value^=' + cus_post_type_selected + '__]').removeAttr('disabled').show();
            meta_terms2.find('option[value^=' + cus_post_type_selected + '__]').removeAttr('disabled').show();

            // select default value "Select Taxonomy"
            cus_taxonomy.val('xxx__select_taxonomy');
            meta_terms1.val('xxx__select_taxonomy');
            meta_terms2.val('xxx__select_taxonomy');
            cus_taxonomy.trigger("chosen:updated");
            meta_terms1.trigger("chosen:updated");
            meta_terms2.trigger("chosen:updated");
        }

        function showTaxonomiesOnclick() {
            //get the value of selected post type option
            var cus_post_type_selected = $('#cpt_post_type').find('option:selected').val();

            //hide all taxonomy elements
            var cus_taxonomy = $('#cus_taxonomy');
            cus_taxonomy.find('option[value]');

            cus_taxonomy.find('option[value]').attr('disabled', 'disabled').hide();

            //show taxonomies that start with post type selected
            cus_taxonomy.find('option[value="xxx__select_taxonomy"]').removeAttr('disabled').show();
            cus_taxonomy.find('option[value^=' + cus_post_type_selected + '__]').removeAttr('disabled').show();

            cus_taxonomy.trigger("chosen:updated");
        }

        function showTerm1OnClick() {

            var cus_post_type_selected = $('#cpt_post_type').find('option:selected').val();

            var meta_terms1 = $('#meta_terms1');
            meta_terms1.find('option[value]');

            meta_terms1.find('option[value]').attr('disabled', 'disabled').hide();
            meta_terms1.find('option[value="xxx__select_taxonomy"]').removeAttr('disabled').show();
            meta_terms1.find('option[value^=' + cus_post_type_selected + '__]').removeAttr('disabled').show();
            meta_terms1.trigger("chosen:updated");
        }

        function showTerm2OnClick() {

            var cus_post_type_selected = $('#cpt_post_type').find('option:selected').val();

            var meta_terms2 = $('#meta_terms2');
            meta_terms2.find('option[value]');

            meta_terms2.find('option[value]').attr('disabled', 'disabled').hide();
            meta_terms2.find('option[value="xxx__select_taxonomy"]').removeAttr('disabled').show();
            meta_terms2.find('option[value^=' + cus_post_type_selected + '__]').removeAttr('disabled').show();
            meta_terms2.trigger("chosen:updated");
        }

        // fill in all the fields on post type change
        $(document).on('change', '#cpt_post_type', updateSelectorsOnChange);

        // fill in taxonomies on taxonomy click (to work with initial load)
        $(document).on('click', '#cus_taxonomy_chosen', showTaxonomiesOnclick);
        $(document).on('click', '#meta_terms1_chosen', showTerm1OnClick);
        $(document).on('click', '#meta_terms2_chosen', showTerm2OnClick);

        //clear terms on taxonomy change
        $(document).on('change', '#cus_taxonomy', function () {

            var cus_terms = $('#cus_terms');
            cus_terms.find('option:selected').removeAttr('selected');
            cus_terms.trigger("chosen:updated");

            var cus_terms_exclude = $('#cus_terms_exclude');
            cus_terms_exclude.find('option:selected').removeAttr('selected');
            cus_terms_exclude.trigger("chosen:updated");
        });


        //show terms for selected taxonomy on click
        $(document).on('click', '#cus_terms_chosen', function () {

            var cus_taxonomy = $('#cus_taxonomy');
            cus_taxonomy.find('option[value]');

            //select terms where option starts with taxonomy text
            var cus_taxonomy_text = $('#cus_taxonomy').find('option:selected').text();
            //console.log('cus_taxonomy_text',cus_taxonomy_text);

            if (cus_taxonomy_text == "Select Taxonomy") cus_taxonomy_text = 'select_taxonomy';
            
            var cus_terms_text = $('#cus_terms').find('option[value^=' + cus_taxonomy_text + '__]').text();
            console.log('cus_terms_text', cus_terms_text);

            $('#cus_terms_chosen').find('li.active-result').each(function () {

                if (cus_terms_text.indexOf($(this).text()) > -1) {
                    
                    $(this).show();
                }
            });
  
         
            
        });

        //show terms to exclude for selected taxonomy on click
        $(document).on('click', '#cus_terms_exclude_chosen', function () {

            var cus_taxonomy = $('#cus_taxonomy');
            cus_taxonomy.find('option[value]');

            //select terms where option starts with taxonomy text
            var cus_taxonomy_text = $('#cus_taxonomy').find('option:selected').text();
            if (cus_taxonomy_text == "Select Taxonomy") cus_taxonomy_text = 'select_taxonomy';
            var cus_terms_exclude_text = $('#cus_terms_exclude').find('option[value^=' + cus_taxonomy_text + '__]').text();

            $('#cus_terms_exclude_chosen').find('li.active-result').each(function () {

                if (cus_terms_exclude_text.indexOf($(this).text()) > -1) {
                   
                    $(this).show();
                }
            });
        });
    });
}(jQuery));
	 