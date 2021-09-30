let row = $('#temp tbody').html(),
   data_list = ``,
   cafe_mall_id = $('#cafe24_mall_id').val().trim(),
   last_page,
   current_page;



$(all_page).each(function (index, item) {
   if (item.page_name == "") {
      item.page_name = item.page_url;
   }
   data_list += `<option value="${item.page_name}" data-pageurl="${item.page_url}">`
});
$('#datalistOptions').html(data_list);





$.ajax({
   type: "GET",
   url: `/api/v1/mall/translatetable?cafe24_mall_id=${cafe_mall_id}`,
   dataType: "json",
   success: function (response) {
      if (response.success) {
         if (response.data != "") {
            let mall_data = response.data.text_data,
               mall_langs = response.data.mall_langs,
               posts_per_page = mall_data.length,
               total_post = response.data.total,
               pagination = `
                  <li class="page-item"><a class="page-link first" href="#"><span aria-hidden="true">&laquo;</span></a></li>
                  <li class="page-item"><a class="page-link previous" href="#"><span aria-hidden="true">&lt;</span></a></li>`;
            last_page = response.data.last_page_url;
            current_page = response.data.current_page;




            if (total_post > 0) {
               let number_of_pages = Math.ceil(total_post / posts_per_page);
               printData(mall_data, mall_langs);
               if (number_of_pages > 1) {
                  for (let i = 1; i <= number_of_pages; i++) {
                     if (i == current_page) {
                        pagination += `<li class="page-item active"><a class="page-link" href="${i}">${i}</a></li>`;
                     } else {
                        pagination += `<li class="page-item"><a class="page-link" href="${i}">${i}</a></li>`;
                     }
                  }
                  pagination += `
                  <li class="page-item"><a class="page-link next" href="#"><span aria-hidden="true">&gt;</span></a></li>
                  <li class="page-item"><a class="page-link last" href="#"><span aria-hidden="true">&raquo;</span></a></li>`;
                  $('.app_pagination').html(pagination);
                  // activePagination(response);
               }
            }

         } else {
            hideLoading();
            Swal.fire({
               icon: 'error',
               title: 'Oops...',
               text: 'No data found!!',
            });
         }


      } else {
         hideLoading();
         Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Something went wrong! Please try again',
         });
      }
   }
});











$('body')
   .on('change', '.page', function () {
      let current_value = $(this).val();
      let match_datalist = $('#datalistOptions option').filter(function () {
         return this.value == current_value;
      }).data('pageurl');
      var page_url = match_datalist ? match_datalist : $(this).val();
      $(this).attr('data-pageurl', page_url)
   })




   .on('click', '.app_more_row', function (e) {
      e.preventDefault();
      $('.app_table tbody').append(row)
   })




   .on('click', '.delete_icon', function () {
      let _this = $(this);
      let row_id = $(this).next().serialize();
      Swal.fire({
         title: 'Are you sure want to remove?',
         showDenyButton: true,
         showCancelButton: false,
         confirmButtonText: 'Yes',
         denyButtonText: `No`,
      }).then((result) => {
         if (result.isConfirmed) {
            if (_this.next().val() !== '0') {
               $.ajax({
                  type: "POST",
                  url: "/api/v1/mall/delete",
                  data: row_id,
                  dataType: "json",
                  success: function (response) {
                     if (response.success) {
                        _this.closest('tr').remove();
                        Swal.fire({
                           icon: 'success',
                           title: 'Row Deleted',
                        });
                     } else {
                        Swal.fire({
                           icon: 'error',
                           title: 'Oops...',
                           text: 'Something went wrong!',
                        });
                     }
                  },
                  error: function (err) {
                     Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                     });
                  }
               });
            } else {
               $(this).closest('tr').remove();
            }
         }
      })
   })




   .on('submit', '#search_form', function (e) {
      e.preventDefault();
      showLoading();
      let criteria = $('.search_criteria select').val(),
         keyword = $('.search_input input').val(),
         url = `/api/v1/mall/search?cafe24_mall_id=${cafe_mall_id}&criteria=${criteria}&keyword=${keyword}`;
      $.ajax({
         type: "GET",
         url: url,
         dataType: "json",
         success: function (response) {
            let search_res = response.data,
               mall_data = search_res.translated_texts,
               mall_langs = search_res.mall_langs;







            if (search_res != '') {
               printData(mall_data, mall_langs);
            } else {
               $('.loading').addClass('hide');
               Swal.fire({
                  icon: 'error',
                  title: 'No result found!!',
               });
            }
         }
      });
   })



   .on('click', '.app_pagination li a', function (e) {
      e.preventDefault();
      showLoading();
      $('.app_pagination li').removeClass('disabled');
      if ($(this).hasClass('first')) {
         $.ajax({
            type: "GET",
            url: `/api/v1/mall/translatetable?page=1&cafe24_mall_id=${cafe_mall_id}`,
            dataType: "json",
            success: function (response) {
               activePagination(response);
            }
         });
      } else if ($(this).hasClass('last')) {
         $.ajax({
            type: "GET",
            url: `${last_page}&cafe24_mall_id=${cafe_mall_id}`,
            dataType: "json",
            success: function (response) {
               activePagination(response);
            }
         });
      } else if ($(this).hasClass('previous')) {
         let url = `/api/v1/mall/translatetable?page=${current_page - 1}&cafe24_mall_id=${cafe_mall_id}`;
         $.ajax({
            type: "GET",
            url: url,
            dataType: "json",
            success: function (response) {
               activePagination(response);
            }
         });
      } else if ($(this).hasClass('next')) {
         let url = `/api/v1/mall/translatetable?page=${current_page + 1}&cafe24_mall_id=${cafe_mall_id}`;
         $.ajax({
            type: "GET",
            url: url,
            dataType: "json",
            success: function (response) {
               activePagination(response);
            }
         });
      } else {
         let page_number = $(this).attr('href');
         let url = `/api/v1/mall/translatetable?page=${page_number}&cafe24_mall_id=${cafe_mall_id}`;
         $.ajax({
            type: "GET",
            url: url,
            dataType: "json",
            success: function (response) {
               activePagination(response);
            }
         });
      }
   });





$('.app_save').click(function (e) {
   e.preventDefault();
   let validate = true;
   $('.app_table select, .app_table input[type=text]').each(function (index, item) {
      if ($.trim($(item).val()) == '') {
         Swal.fire({
            icon: 'error',
            title: 'Please fill out all the fields',
         });
         validate = false;
         return validate;
      }
   });

   $('.app_table input.page').each(function (index, item) {
      if ($.trim($(item).attr('data-pageurl')) == '') {
         Swal.fire({
            icon: 'error',
            title: 'Please fill out all the fields',
         });
         validate = false;
         return validate;
      }
   });
   if (validate) {
      let page = ``;
      let data = $('#app_form').serialize();
      $('#app_form input.page').each(function (index, item) {
         page += "&page_url[]=" + $(item).attr('data-pageurl');
      });
      data = data + page;
      $('.loading').addClass('show');
      $.ajax({
         type: "POST",
         url: "/api/v1/mall/text",
         data: data,
         dataType: "json",
         success: function (response) {
            $('.loading').removeClass('show');
            if (response.success) {
               let row_id = response.data;
               if (row_id.length > 0) {
                  $('.app_table .row_id').each(function (index, item) {
                     $(item).val(row_id[index]);
                  });
               }
               Swal.fire({
                  icon: 'success',
                  title: 'Congratulation!',
                  text: 'Your work has been saved',
               })
            } else {
               $('.loading').removeClass('show');
               Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Something went wrong!',
               })
            }
         },
         error: function (err) {
            $('.loading').removeClass('show');
            Swal.fire({
               icon: 'error',
               title: 'Oops...',
               text: 'Something went wrong!',
            });
         }
      });
   }

});
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
   return new bootstrap.Tooltip(tooltipTriggerEl)
})








function activePagination(response) {
   let current_page = response.data.current_page,
      last_page = response.data.last_page;
   if (response.data.text_data.length > 0) {
      let mall_data = response.data.text_data,
         mall_langs = response.data.mall_langs;
      printData(mall_data, mall_langs);
   }
   $('.app_pagination li').each(function (index, item) {
      $(item).removeClass('active');
      if (Number($(item).children().attr('href')) == current_page) {
         $(item).addClass('active');
      }
      if (current_page == 1) {
         $(this).find('.first, .previous').parent().addClass('disabled');
      } else if (current_page == last_page) {
         $(this).find('.last, .next').parent().addClass('disabled');

      }
   });
   hideLoading();
}


function showLoading() {
   $('.loading').removeClass('hide');
}

function hideLoading() {
   $('.loading').addClass('hide');
}



function printData(mall_data, mall_langs) {
   if (mall_data != '') {
      let html = ``;
      $(mall_data).each(function (index, item) {
         let mall_langs_html = ``;

         if (item.is_placeholder == '1') {
            placeholder_html = `
                  <option value="0">No</option>
                  <option value="1" selected="selected">Yes</option>
               `;
         } else {
            placeholder_html = `
                  <option value="0" selected="selected">No</option>
                  <option value="1">Yes</option>
               `;
         }
         if (mall_langs != "") {
            $(mall_langs).each(function (idx, itm) {
               if (itm.lang_code == item.language) {
                  mall_langs_html += `<option selected="selected" value='${itm.lang_code}'>${itm.shop_name}</option>`;
               } else {
                  mall_langs_html += `<option value='${itm.lang_code}'>${itm.shop_name}</option>`;
               }
            });

            html += `<tr>
                     <td>
                        <input type="text" name="page_name[]" class="form-control page" list="datalistOptions" placeholder="Type to search..." data-pageurl='${item.page_url}' value="${item.page_name}">
                     </td>
                     <td>
                        <div class="input-group flex-nowrap">
                           <input type="text" class="form-control selector" placeholder="Selector" aria-describedby="addon-wrapping" name="selector[]" value='${item.selector}'>
                        </div>
                     </td>
                     <td>
                        <select class="form-select language" name="language[]">
                           ${mall_langs_html}
                        </select>
                     </td>
                     <td>
                        <div class="input-group flex-nowrap">
                           <input type="text" class="form-control input_text" placeholder="Translate Text" aria-describedby="addon-wrapping" name="input_text[]" value='${item.input_text}'>
                        </div>
                     </td>
                     <td>
                        <select class="form-select table_checkbox" name="is_placeholder[]">
                           ${placeholder_html}
                        </select>
                     </td>
                     <td>
                        <span class="delete_icon" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Click to remove" aria-label="Click to remove">
                           <i class="far fa-trash-alt"></i>
                           </span>
                           <input type="hidden" name="row_id[]" class="row_id" value='${item.row_id}'>
                     </td>
                  </tr>`;
         }
      });

      $('.app_table tbody').html(html);

   }

   hideLoading();

}