var $ = window.$; // use the global jQuery instance

if ($("#my-awesome-dropzone").length > 0) {
    var token = $('input[name=_token]').val();

    // A quick way setup
    var myDropzone = new Dropzone("#my-awesome-dropzone", {
        // Setup chunking
        chunking: true,
        method: "POST",
        maxFilesize: 12,
        chunkSize: 100000,
        // If true, the individual chunks of a file are being uploaded simultaneously.
        parallelChunkUploads: false
    });

    // Append token to the request - required for web routes
    myDropzone.on('sending', function (file, xhr, formData) {
        formData.append("_token", token);
    })
}

if ($("#mediaZoneUpload").length > 0) {

    var mediaDropzone = new Dropzone("#mediaZoneUpload", {
        // Setup chunking
        chunking: true,
        method: "POST",
        maxFilesize: 2048,
        chunkSize: 2000000,
        // If true, the individual chunks of a file are being uploaded simultaneously.
        parallelChunkUploads: false,
        dictDefaultMessage : 'Chọn file upload !',
        dictFileTooBig: "File quá lớn, file tối đa : {{maxFilesize}} MB.",
    });

};



// Append token to the request - required for web routes
mediaDropzone.on('sending', function (file, xhr, formData) {
    formData.append("_token", token);
});