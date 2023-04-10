//https://innostudio.de/fileuploader/documentation/#input-attributes
$(document).ready(function() {
	// enable fileuploader plugin
	$('input[name="data[Support][attachment]"]').fileuploader({
		addMore: true,
		captions: {
			button: function (options) {
				return 'Choisir';
				//return 'Choisir ' + (options.limit == 1 ? 'fichier' : 'fichiers');
			},
			feedback: function(options) {
				return 'Choisir ' + (options.limit == 1 ? 'le fichier' : 'les fichiers') + ' à envoyer';
			},
			feedback2: function(options) {
				return options.length + ' ' + (options.length > 1 ? 'fichiers sélectionnés' : 'fichier sélectionné');
			},
			fileName: 'Un fichier avec le même nom ${name} a été déjà sélectionné.',
			folderUpload: 'Les dossiers ne sont pas permis.',
       errors: {
          filesLimit: function(options) {
              return 'Seulement ${limit} ' + (options.limit == 1 ? 'fichier' : 'fichiers') + ' autorisé.'
          },
          filesType: 'Seulement ${limit} fichiers sont autorisé',
          fileSize: '${name} est trop volumineux! Veuillez choisir un fichier jusqu\'à ${fileMaxSize} Mo.',
          filesSizeAll: 'Les fichiers choisis sont trop volumineux! Veuillez sélectionner des fichiers jusqu\'à ${maxSize} Mo.',
          fileName: 'Un fichier avec le même nom ${name} a été déjà sélectionné.',
          remoteFile: 'Remote files are not allowed.',
          folderUpload: 'Les dossiers ne sont pas permis.',
      }
		},
		thumbnails: {
			item: '<li class="fileuploader-item" style="margin-bottom: 0px;padding: 5px 16px 10px 5px;">' +
				'<div class="columns">' +
				'<div class="column-thumbnail" style="display: none">${image}<span class="fileuploader-action-popup"></span></div>' +
				'<div class="column-title">' +
				'<div title="${name}">${name}</div>' +
				'<span>${size2}</span>' +
				'</div>' +
				'<div class="column-actions">' +
				'<button class="fileuploader-action fileuploader-action-remove" title="${captions.remove}"><i class="fileuploader-icon-remove"></i></a>' +
				'</div>' +
				'</div>' +
				'<div class="progress-bar2">${progressBar}<span></span></div>' +
				'</li>'
		}
	});
});
