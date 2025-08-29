<?php include __DIR__ . "/captcha_init.php"; ?>
<?php $token = $_SESSION['slider_captcha']['token']; ?>

<div class="nvc-captcha">
  <label style="display:none;">Leave this field empty</label>
  <input type="text" name="nvc_trap" style="display:none;"> <!-- Honeypot -->

  <div class="nvc-bar" id="nvc-bar">
    <div class="nvc-gap" id="nvc-gap">?</div>
    <div class="nvc-knob" id="nvc-knob">⇾</div>
  </div>
  <div class="nvc-status" id="nvc-status"></div>

  <input type="hidden" name="captcha_verified" id="nvc-captcha_verified" value="0">
  <input type="hidden" id="nvc-captcha_token" value="<?php echo $token; ?>">
</div>

<style>
.nvc-captcha { background:#fff; padding:15px; border:1px solid #ddd; border-radius:10px; width:320px; margin-top:10px; font-family:Arial, sans-serif; }
.nvc-captcha .nvc-bar { position:relative; background:linear-gradient(90deg,#f5f5f5,#e8e8e8); height:55px; border-radius:10px; overflow:hidden; }
.nvc-captcha .nvc-gap { position:absolute; top:7px; width:70px; height:40px; background:rgba(0,0,0,0.08); border-radius:6px; text-align:center; line-height:40px; font-weight:bold; color:#444; user-select:none; }
.nvc-captcha .nvc-knob { position:absolute; top:7px; left:5px; width:55px; height:40px; background:#fff; border-radius:8px;
        box-shadow:0 2px 8px rgba(0,0,0,.25); display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:18px; font-weight:bold; user-select:none; transition:background .2s; }
.nvc-captcha .nvc-knob:active { background:#f0f0f0; }
.nvc-captcha .nvc-status { margin-top:8px; font-size:13px; }
.nvc-captcha .success { color:green; }
.nvc-captcha .error { color:red; }
</style>

<script>
const bar = document.getElementById('nvc-bar');
const knob = document.getElementById('nvc-knob');
const gap  = document.getElementById('nvc-gap');
const status = document.getElementById('nvc-status');
const token = document.getElementById('nvc-captcha_token').value;
const hiddenField = document.getElementById('nvc-captcha_verified');

let dragging=false, startX=0, knobStart=0;
const knobW=knob.offsetWidth, gapW=gap.offsetWidth;

function setKnob(left){
  const max=bar.clientWidth-knobW-5, min=5;
  left=Math.max(min, Math.min(max,left));
  knob.style.left=left+"px"; knob._left=left;
}

knob.addEventListener("mousedown", e=>{
  dragging=true; startX=e.clientX; knobStart=knob._left||5;
});
document.addEventListener("mousemove", e=>{
  if(!dragging) return;
  setKnob(knobStart+(e.clientX-startX));
});
document.addEventListener("mouseup", ()=>{
  if(!dragging) return;
  dragging=false;
  const percent=Math.round(((knob._left||5)/(bar.clientWidth-knobW-5))*100);
  status.textContent="Verifying...";
  fetch("nvcaptcha/captcha_verify.php", {
    method:"POST",
    headers:{"Content-Type":"application/json"},
    body:JSON.stringify({pos:percent,token})
  })
  .then(r=>r.json())
  .then(res=>{
    if(res.ok){
      status.textContent=`Matched ${res.match}% ✔`; status.className="success";
      knob.style.pointerEvents="none"; knob.style.background="#d1f7d1"; knob.textContent="✓";
      hiddenField.value="1";
    } else {
      status.textContent=res.msg||"Failed"; status.className="error";
      setKnob(5);
    }
  });
});

// Draw gap
fetch("nvcaptcha/captcha_verify.php", {
  method:"POST", headers:{"Content-Type":"application/json"},
  body:JSON.stringify({action:"display",token})
})
.then(r=>r.json()).then(res=>{
  if(res.ok){
    const left=Math.round((res.pos/100)*(bar.clientWidth-gapW));
    gap.style.left=left+"px";
  }
});
</script>
