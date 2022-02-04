import { scanImageData } from 'zbar.wasm';

const SCAN_PROID_MS = 800;

var mediaStream = null;

const video = document.getElementById('video');

const init = async () => {
  mediaStream = await navigator.mediaDevices.getUserMedia({
      audio: false,
      video: {
        facingMode: 'environment'
      }
    });
  video.srcObject = mediaStream;
  video.setAttribute('playsinline', '');
  video.play();
  await new Promise(r => {
    video.onloadedmetadata = r;
	document.dispatchEvent(new Event('play'));
  });
};

const scan = async () => {
  const canvas = document.createElement('canvas');
  const video = document.getElementById('video');
  const width = video.videoWidth;
  const height = video.videoHeight;
  canvas.width = width;
  canvas.height = height;
  const ctx = canvas.getContext('2d');
  ctx.drawImage(video, 0, 0, width, height);
  const imgData = ctx.getImageData(0, 0, width, height);
  const res = await scanImageData(imgData);
  
  for (let i = 0; i < res.length; ++i) {
    const sym = res[i];
	document.dispatchEvent(new CustomEvent('decode', { detail: { type: sym.typeName, value: sym.decode() } }));
  }
};

const sleep = ms => new Promise(r => { setTimeout(r, ms) });

const main = async () => {
  try {
    await init();
    while (video.srcObject != null) {
      await scan();
      await sleep(SCAN_PROID_MS);
    }
  } catch (err) {
    const div = document.createElement('div');
    div.innerText = 'Cannot get cammera: ' + err;
    document.body.appendChild(div);
    console.error(err);
  }
};

const stop = async () => {
	video.pause();
	
	if(mediaStream != null ) {
		mediaStream.getTracks().map(function(val) {
			val.stop();
		});
	}
	
	video.srcObject = null;
}

document.addEventListener('scan', main, false);

document.addEventListener('stop', stop, false);