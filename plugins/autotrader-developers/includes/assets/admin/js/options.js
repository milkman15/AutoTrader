jQuery(function ($) {
    // on upload button click
    $('body').on('click', '.at-upload', function (event) {
        event.preventDefault(); // prevent default link click and page refresh

        const button = $(this)
        const imageId = button.next().next().val();

        const customUploader = wp.media({
            title: 'Insert image(s)',
            library: {
                type: 'image'
            },
            button: {
                text: 'Use image(s)'
            },
            multiple: true
        }).on('select', function () {
            const attachment = customUploader.state().get('selection');
            const attachmentIds = [];
            const existingIds = [];
            const existingImages = document.querySelectorAll('.at-gallery-image');

            if (existingImages && existingImages.length > 0) {
                [...existingImages].map(images => {
                    existingIds.push(images.dataset.attachid);
                })
            }
            attachment.map(image => {
                let imageUrl = image.toJSON().url;
                let imageAlt = image.toJSON().alt;
                let imageId = image.toJSON().id.toString();
                if (!existingIds.includes(imageId)) {
                    attachmentIds.push(imageId);
                    document.querySelector('#at-gallery-preview').innerHTML += (`<div class="at-gallery-image" data-attachId="${imageId}"><a href="#" class="at-remove">Remove image</a><img src="${imageUrl}" alt="${imageAlt}" width="300" /></div>`);
                }
            })
            button.html('Add more images');
            $('.at-remove').show();
            console.log(attachmentIds.join());
            $('#at-gallery-file').val($('#at-gallery-file').val() + "," + attachmentIds.join());
        })

        customUploader.on('open', function () {

            if (imageId) {
                const selections = customUploader.state().get('selection')
                attachment = wp.media.attachment(imageId);
                attachment.fetch();
                selections.map(selection => {
                    selection.add(attachment ? [attachment] : []);
                })
            }

        })
        customUploader.open()

    });

    $('body').on('click', '.at-remove', function (event) {
        event.preventDefault();
        const button = $(this);
        let oldVals = document.querySelector('#at-gallery-file').value.split(',');
        let newVal = oldVals.filter(val => val !== button.closest('.at-gallery-image')[0].dataset.attachid);
        document.querySelector('#at-gallery-file').value = newVal;
        button.closest('.at-gallery-image').remove();


        if (document.querySelectorAll('.at-gallery-image').length === 0) {
            $('.at-upload').html('Upload image(s)');
        }
    });
});
