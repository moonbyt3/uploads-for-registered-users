/*
 * Better File Input 1.3.0 (https://github.com/nifte/better-file-input)
 * by nifte (https://github.com/nifte)
 * Licensed under MIT (https://github.com/nifte/better-file-input/blob/master/LICENSE)
 */

// Define styles
const style = '@-webkit-keyframes file_grow{0%{max-height:0;padding:0 10px}100%{max-height:100px}}@keyframes file_grow{0%{max-height:0;padding:0 10px}100%{max-height:100px}}@-webkit-keyframes shadow_grow{0%{-webkit-transform:scale(0);transform:scale(0)}100%{-webkit-transform:scale(1);transform:scale(1)}}@keyframes shadow_grow{0%{-webkit-transform:scale(0);transform:scale(0)}100%{-webkit-transform:scale(1);transform:scale(1)}}.bfi-container{display:block;position:relative;width:100%;height:unset;margin:0;padding:0;border-radius:5px;background:#f0f0f0;-webkit-box-sizing:border-box;box-sizing:border-box;-webkit-transition:max-height 1s ease;transition:max-height 1s ease}.bfi-container.expanded{border:4px dashed gray}.bfi-container *{-webkit-box-sizing:border-box;box-sizing:border-box}.bfi-converted,.bfi-converted-multi{opacity:0;position:absolute;top:0;left:0;width:100%;height:100%}.bfi-container:not(.expanded) .bfi-converted,.bfi-container:not(.expanded) .bfi-converted-multi{z-index:-10}.bfi-container.expanded .bfi-converted,.bfi-container.expanded .bfi-converted-multi{z-index:20}.bfi-label,.bfi-label-selected{border:1px dotted black;border-radius: 4px;display:inline-block;width:100%;height:unset;margin-bottom:10px;text-align:center;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;z-index:10}.bfi-container:not(.expanded) .bfi-label,.bfi-label-selected{padding:40px 20px}.bfi-container.expanded .bfi-label{padding:40px 20px}.bfi-label{-webkit-transition:padding .25s ease;transition:padding .25s ease}.bfi-clear,.bfi-label span{cursor:pointer;text-decoration:underline}.bfi-file{display:inline-block;width:auto;padding:10px;background:#646464;background:-webkit-gradient(linear,left bottom,left top,from(rgba(90,90,90,1)),color-stop(75%,rgba(110,110,110,1)));background:linear-gradient(0deg,rgba(90,90,90,1) 0,rgba(110,110,110,1) 75%);color:#fff;border-radius:7px;z-index:10;line-height:1em;-webkit-animation:file_grow .7s ease;animation:file_grow .7s ease}.bfi-converted~.bfi-file{margin:10px}.bfi-converted-multi~.bfi-file{margin:0 10px 10px 10px}.bfi-file i{font-style:normal;font-size:.8em;color:#b4b4b4}.bfi-file .bfi-clear{position:absolute;right:25px;top:calc(50% - 2px);-webkit-transform:translateY(-50%);transform:translateY(-50%);-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}.bfi-shadow-container{position:absolute;display:none;margin:0;padding:0;left:0;right:0;top:0;bottom:0;clip:rect(0,auto,auto,0);z-index:15}.bfi-container.expanded .bfi-shadow-container{display:unset}.bfi-shadow{position:absolute;display:none;width:350px;height:350px;border-radius:50%;background:rgba(0,0,0,.06);-webkit-transition:left .1s ease,top .1s ease;transition:left .1s ease,top .1s ease}.bfi-container.hovering .bfi-shadow{display:unset;-webkit-animation:shadow_grow .5s ease;animation:shadow_grow .5s ease} .bfi-files{display: flex; gap: 15px; margin-bottom: 15px;}';

// Initialize function
var bfi_counter = 0;
function bfi_init(options = null) {
	if (document.querySelectorAll('.bfi-style').length < 1) document.body.insertAdjacentHTML('beforeend', `<style class="bfi-style">${style}</style>`);
	let bfi = document.querySelectorAll('input[type="file"].bfi');
	let total = bfi.length;
	for (let i = 0; i < total; i++) {
		bfi_counter++;
		let input = bfi[i];
		let container = document.createElement('div');
		let label = '';
		input.parentElement.insertBefore(container, input);
		container.appendChild(input);
		container.classList.add('bfi-container');
		if (input.hasAttribute('multiple')) {
			input.classList.add('bfi-converted-multi');
			container.insertAdjacentHTML('beforeend', '<div class="bfi-label-selected" style="display: none;"></div>');
			label = 'Drag & Drop files here, or <span>Browse</span>';
		} else {
			input.classList.add('bfi-converted');
			label = 'Drag & Drop file here, or <span>Browse</span>';
		}
		if (!input.hasAttribute('id')) input.setAttribute('id', `bfi-${bfi_counter}`);
		let id = input.getAttribute('id');
		container.insertAdjacentHTML('afterbegin', `<label class="bfi-label" for="${id}" tabindex="0">${label}</label>`);
		container.insertAdjacentHTML('beforeend', '<div class="bfi-shadow-container"><div class="bfi-shadow"></div></div>');
        container.insertAdjacentHTML('beforeend', '<div class="bfi-files"></div>');
		input.setAttribute('tabindex', -1);
	}
	document.querySelectorAll('input[type="file"].bfi').forEach(el => { el.classList.remove('bfi'); });
	if (options != null) {
		let style_override = '';
		if (options.hasOwnProperty('labelColor')) style_override += `.bfi-label, .bfi-label-selected { color: ${options.labelColor} }`;
		if (options.hasOwnProperty('containerColor')) style_override += `.bfi-container { background: ${options.containerColor} }`;
		if (options.hasOwnProperty('fileColor')) style_override += `.bfi-file { background: ${options.fileColor} }`;
		if (options.hasOwnProperty('fileNameColor')) style_override += `.bfi-file { color: ${options.fileNameColor} }`;
		if (options.hasOwnProperty('fileInfoColor')) style_override += `.bfi-file i { color: ${options.fileInfoColor} }`;
		if (options.hasOwnProperty('dragDropBorder')) style_override += `.bfi-container.expanded { border: ${options.dragDropBorder} }`;
		document.body.insertAdjacentHTML('beforeend', `<style class="bfi-style-override">${style_override}</style>`);
	}
}

// Reset style overrides
function bfi_reset() {
	let styles = document.querySelectorAll('.bfi-style-override');
	if (styles.length) {
		styles.forEach(el => {
			el.remove();
		});
	}
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', () => {
	bfi_init();
});

// Drag files onto page
var bfi_drag_timeout, bfi_hover_timeout;
window.addEventListener('dragover', e => {
	let dt = e.dataTransfer;
	if (dt.types && (dt.types.indexOf ? dt.types.indexOf('Files') != -1 : dt.types.contains('Files'))) {
		document.querySelectorAll('.bfi-container').forEach(el => {
			if (el.querySelector('.bfi-label').style.display != 'none') el.classList.add('expanded');
		});
		clearTimeout(bfi_drag_timeout);
		bfi_drag_timeout = setTimeout(() => {
			document.querySelectorAll('.bfi-container').forEach(el => { el.classList.remove('expanded'); });
		}, 100);
		document.querySelectorAll('.bfi-shadow').forEach(el => {
			let container = el.closest('.bfi-shadow-container').getBoundingClientRect();
			let size = Number(container.width * 0.75).toFixed();
			if (size > 500) size = 500;
			el.style.width = size + 'px';
			el.style.height = size + 'px';
			el.style.left = Number(e.pageX - container.left - (size / 2)).toFixed() + 'px';
			el.style.top = Number(e.pageY - container.top - (size / 2)).toFixed() + 'px';
		});
		if (e.target.classList.contains('bfi-converted') || e.target.classList.contains('bfi-converted-multi')) {
			let container = e.target.closest('.bfi-container');
			container.classList.add('hovering');
			clearTimeout(bfi_hover_timeout);
			bfi_hover_timeout = setTimeout(() => {
				container.classList.remove('hovering');
			}, 100);
		}
	}
});

// Drag files out of container
window.addEventListener('dragleave', e => {
	if (e.target.classList.contains('bfi-converted') || e.target.classList.contains('bfi-converted-multi')) {
		let container = e.target.closest('.bfi-container');
		container.classList.remove('hovering');
	}
});

// Prevent browser from opening any dragged files
window.addEventListener('dragover', e => {
	if (!e.target.classList.contains('bfi-converted') && !e.target.classList.contains('bfi-converted-multi')) {
		e.preventDefault();
		e.dataTransfer.effectAllowed = 'none';
		e.dataTransfer.dropEffect = 'none';
	}
});

// Prevent browser from opening any dropped files
window.addEventListener('drop', e => {
	if (!e.target.classList.contains('bfi-converted') && !e.target.classList.contains('bfi-converted-multi')) {
		e.preventDefault();
		e.dataTransfer.effectAllowed = 'none';
		e.dataTransfer.dropEffect = 'none';
	}
});

function bytesToSize(bytes) {
	const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
	if (bytes === 0) return 'n/a'
	const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)), 10)
	if (i === 0) return `${bytes} ${sizes[i]}`
	return `${(bytes / (1024 ** i)).toFixed(1)} ${sizes[i]}`
}

// Watch for file updates
document.addEventListener('change', e => {
	if (e.target.classList.contains('bfi-converted')) {
		let container = e.target.closest('.bfi-container');
		if (e.target.files.length) {
			container.querySelector('.bfi-label').style.display = 'none';
			container.querySelectorAll('.bfi-file').forEach(el => { el.remove(); });
			let file = e.target.files[0].name;
			let size = Number(e.target.files[0].size / 1000).toFixed(1) + ' KB';
			container.insertAdjacentHTML('beforeend', `<div class="bfi-file"><span class="bfi-clear" tabindex="0">Undo</span>${file}<br><i>${size}</i></div>`);
		} else {
			container.querySelector('.bfi-label').style.display = '';
			container.querySelectorAll('.bfi-file').forEach(el => { el.remove(); });
		}
	}
	if (e.target.classList.contains('bfi-converted-multi')) {
		let container = e.target.closest('.bfi-container');
        let filesContainer = document.querySelector('.bfi-files');
		if (e.target.files.length) {
			container.querySelector('.bfi-label').style.display = 'none';
			container.querySelector('.bfi-label-selected').style.display = '';
			container.querySelectorAll('.bfi-file').forEach(el => { el.remove(); });

			const {ufru_max_file_size} = pluginSettings; // Localized variable from WordPress

			let files = [];
			for (let i = 0; i < e.target.files.length; i++) {
                let file = e.target.files[i];
                if (convert_bytes_to_megabytes(file.size) <= ufru_max_file_size) { // Check if file size is less than or equal to 2 megabytes
                    files.push({
                        'name': file.name,
                        'size': Number(file.size / 1000).toFixed(1) + ' KB',
                        'url': URL.createObjectURL(file)
                    });
                } else {
                    alert(`File "${file.name}" exceeds the ${ufru_max_file_size}MB size limit and will not be uploaded.`);
                }
			}
			if (files.length > 0) {
				let fileCount = '1 file';
				if (files.length > 1) fileCount = `${files.length} files`;
				container.querySelector('.bfi-label-selected').innerHTML = `${fileCount} selected. <span class="bfi-clear" tabindex="0">Undo</span>`;
				files.forEach(file => {
					filesContainer.insertAdjacentHTML('beforeend', `
						<div class="bfi-file">
							<img src="${file.url}" onerror="this.onerror=null;this.src='https\:\/\/placehold.co/200x200?text=File:\\n${file.name}' "width="130" height="130" style="object-fit: cover;"/> <br>
							${file.name}<br>
							<i>${file.size}</i>
						</div>
					`);
				});
			} else {
				container.querySelector('.bfi-label').style.display = 'block';
				container.querySelector('.bfi-label-selected').style.display = 'none';
			}
		} else {
			container.querySelector('.bfi-label').style.display = '';
			container.querySelector('.bfi-label-selected').style.display = 'none';
			container.querySelectorAll('.bfi-file').forEach(el => { el.remove(); });
		}
	}
});

// Simulate click on focused bfi element
document.addEventListener('keyup', e => {
	if (e.keyCode == 32 || e.keyCode == 13) {
		if (document.activeElement.classList.contains('bfi-label')) document.activeElement.click();
		if (document.activeElement.classList.contains('bfi-clear')) document.activeElement.click();
	}
});

// Clear files on undo
document.addEventListener('click', e => {
	if (e.target.classList.contains('bfi-clear')) {
		let container = e.target.closest('.bfi-container');
		let inputID = container.querySelector('.bfi-converted, .bfi-converted-multi').getAttribute('id');
		bfi_clear(`#${inputID}`);
	}
});

// Clear files from a bfi element
function bfi_clear(query = null) {
	if (query == null) query = '.bfi-converted, .bfi-converted-multi';
	let inputs = document.querySelectorAll(query);
	if (inputs.length) {
		inputs.forEach(el => {
			el.value = '';
			el.dispatchEvent(new Event('change', { 'bubbles': true }));
		});
	}
}

function convert_bytes_to_megabytes(bytes) {
	if (!bytes) {
		console.log('Error: parameter passed to this function is not defined');
		return;
	} else {
		return (bytes / (1024 * 1024)).toFixed(1);
	}
}