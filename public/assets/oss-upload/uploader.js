function uploader(elementId, module, callback, uploader, options, ossfile) {
    options = options ? options : {};

    new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',
        browse_button: elementId,
        multi_selection: options.hasOwnProperty("multiple") ? options.multiple : false,
        flash_swf_url: 'lib/plupload-2.1.2/js/Moxie.swf',
        silverlight_xap_url: 'lib/plupload-2.1.2/js/Moxie.xap',
        url: 'http://oss.aliyuncs.com',

        filters: {
            mime_types: options.hasOwnProperty("mime_types") ? options.mime_types : [
                {title: "Image files", extensions: "jpg,jpeg,png,gif"},
            ],
            max_file_size: options.hasOwnProperty("max_file_size") ? options.max_file_size : '30mb', //最大只能上传10mb的文件
            prevent_duplicates: true //不允许选取重复文件
        },

        init: {

            FilesAdded: function (up, files) {
                uploadModule = module;
                uploaderId = uploader;
                ossfileId = ossfile;
                set_upload_param(up, '', false);
                plupload.each(files, function (file) {
                    document.getElementById(ossfileId).innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ')<b></b>'
                        + '<div class="progress"><div class="progress-bar" style="width: 0%"></div><span class="oss-100">100%</span></div>'
                        + '</div>';
                });
            },

            BeforeUpload: function (up, file) {
                set_upload_param(up, file.name, true);
            },

            UploadProgress: function (up, file) {
                var d = document.getElementById(file.id);
                d.getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
                var prog = d.getElementsByTagName('div')[0];
                var progBar = prog.getElementsByTagName('div')[0]
                progBar.style.width = 3 * file.percent + 'px';
                progBar.setAttribute('aria-valuenow', file.percent);
            },

            FileUploaded: function (up, file, info) {
                if (info.status == 200) {
                    document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = 'upload success';
                    callback(g_object_name, file.getNative());
                } else {
                    document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = info.response;
                }
            },

            Error: function (up, err) {
                console.log(err);
            }
        }
    }).init();


};
