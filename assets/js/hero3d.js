/* Hero 3D: lensa kaca melayang di depan busur cahaya indigo (three.js + bloom). */
import * as THREE from 'three';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';
import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js';
import { RenderPass } from 'three/addons/postprocessing/RenderPass.js';
import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js';
import { OutputPass } from 'three/addons/postprocessing/OutputPass.js';

export async function createHero(canvas, { reduce = false, getProgress = () => 0 } = {}) {
    const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, powerPreference: 'high-performance' });
    renderer.setPixelRatio(Math.min(devicePixelRatio, 1.75));
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 0.9;
    renderer.setClearColor(0x05040c, 1);

    const scene = new THREE.Scene();
    const pmrem = new THREE.PMREMGenerator(renderer);
    scene.environment = pmrem.fromScene(new RoomEnvironment(), 0.04).texture;

    const camera = new THREE.PerspectiveCamera(34, 1, 0.1, 100);
    camera.position.set(0, 0, 9);

    const world = new THREE.Group();
    scene.add(world);

    /* --- Busur cahaya (setengah torus berbentuk U) --- */
    const arcUniforms = {
        uTime: { value: 0 },
        uReveal: { value: reduce ? 1 : 0 },
        uColA: { value: new THREE.Color('#2a12d8') },
        uColB: { value: new THREE.Color('#d6d1ff') },
    };
    const arcMat = new THREE.ShaderMaterial({
        uniforms: arcUniforms,
        transparent: true,
        depthWrite: false,
        blending: THREE.AdditiveBlending,
        vertexShader: /* glsl */`
            varying vec2 vUv; varying vec3 vN; varying vec3 vView;
            void main() {
                vUv = uv;
                vec4 mv = modelViewMatrix * vec4(position, 1.0);
                vN = normalize(normalMatrix * normal);
                vView = normalize(-mv.xyz);
                gl_Position = projectionMatrix * mv;
            }`,
        fragmentShader: /* glsl */`
            uniform float uTime; uniform float uReveal; uniform vec3 uColA; uniform vec3 uColB;
            varying vec2 vUv; varying vec3 vN; varying vec3 vView;
            void main() {
                float u = vUv.x;
                float ends = smoothstep(0.0, 0.32, u) * smoothstep(1.0, 0.68, u);
                float grow = 1.0 - smoothstep(uReveal * 0.52 - 0.04, uReveal * 0.52, abs(u - 0.5));
                float fres = pow(1.0 - abs(dot(vN, vView)), 1.6);
                float shimmer = 0.85 + 0.15 * sin(u * 18.0 - uTime * 1.2);
                vec3 col = mix(uColA, uColB, clamp(fres * 1.1 + ends * 0.35, 0.0, 1.0));
                float a = ends * grow * (0.25 + fres * 0.95) * shimmer;
                gl_FragColor = vec4(col * (0.6 + ends * 0.9), a);
            }`,
    });
    const arc = new THREE.Mesh(new THREE.TorusGeometry(2.75, 0.34, 64, 220, Math.PI), arcMat);
    arc.rotation.z = Math.PI; // bukaan menghadap ke atas
    arc.position.set(0, 0.55, -1.2);
    world.add(arc);

    /* --- Cincin orbit tipis --- */
    const ringMat = new THREE.MeshBasicMaterial({ color: 0xb1a7ff, transparent: true, opacity: 0.55 });
    const ring = new THREE.Mesh(new THREE.TorusGeometry(3.6, 0.006, 8, 320), ringMat);
    ring.rotation.set(1.25, 0.2, 0);
    world.add(ring);
    const ring2 = new THREE.Mesh(new THREE.TorusGeometry(2.05, 0.004, 8, 240), ringMat.clone());
    ring2.material.opacity = 0.35;
    ring2.rotation.set(1.45, -0.35, 0);
    world.add(ring2);

    /* --- Lensa kaca --- */
    const glass = new THREE.MeshPhysicalMaterial({
        color: 0xffffff,
        metalness: 0,
        roughness: 0.04,
        transmission: 1,
        thickness: 1.6,
        ior: 1.42,
        iridescence: 0.55,
        iridescenceIOR: 1.3,
        attenuationColor: new THREE.Color('#8a7bff'),
        attenuationDistance: 2.4,
        clearcoat: 1,
        clearcoatRoughness: 0.05,
        envMapIntensity: 0.7,
    });
    const orb = new THREE.Mesh(new THREE.SphereGeometry(1.2, 96, 96), glass);
    orb.position.set(0, -0.15, 0.4);
    world.add(orb);

    // Inti bercahaya di dalam lensa
    const core = new THREE.Mesh(
        new THREE.SphereGeometry(0.28, 48, 48),
        new THREE.MeshBasicMaterial({ color: new THREE.Color('#c9c2ff').multiplyScalar(1.15) })
    );
    core.position.copy(orb.position).add(new THREE.Vector3(0, 0, -0.2));
    world.add(core);

    // Cincin kaca kecil di sekeliling lensa
    const halo = new THREE.Mesh(new THREE.TorusGeometry(1.62, 0.05, 32, 200), glass.clone());
    halo.material.thickness = 0.4;
    halo.position.copy(orb.position);
    halo.rotation.set(1.2, 0.25, 0);
    world.add(halo);

    /* --- Partikel debu cahaya --- */
    const COUNT = 1600;
    const pos = new Float32Array(COUNT * 3);
    const seed = new Float32Array(COUNT);
    for (let i = 0; i < COUNT; i++) {
        const r = 2.2 + Math.pow(Math.random(), 0.6) * 6;
        const th = Math.random() * Math.PI * 2;
        const ph = Math.acos(2 * Math.random() - 1);
        pos[i * 3] = r * Math.sin(ph) * Math.cos(th);
        pos[i * 3 + 1] = r * Math.cos(ph) * 0.7;
        pos[i * 3 + 2] = r * Math.sin(ph) * Math.sin(th) - 2;
        seed[i] = Math.random();
    }
    const pGeo = new THREE.BufferGeometry();
    pGeo.setAttribute('position', new THREE.BufferAttribute(pos, 3));
    pGeo.setAttribute('aSeed', new THREE.BufferAttribute(seed, 1));
    const pMat = new THREE.ShaderMaterial({
        transparent: true, depthWrite: false, blending: THREE.AdditiveBlending,
        uniforms: { uTime: arcUniforms.uTime, uPx: { value: renderer.getPixelRatio() } },
        vertexShader: /* glsl */`
            attribute float aSeed; uniform float uTime; uniform float uPx; varying float vA;
            void main() {
                vec3 p = position;
                p.y += sin(uTime * 0.3 + aSeed * 30.0) * 0.12;
                vec4 mv = modelViewMatrix * vec4(p, 1.0);
                gl_Position = projectionMatrix * mv;
                gl_PointSize = (1.0 + aSeed * 2.2) * uPx * (6.0 / -mv.z);
                vA = 0.25 + 0.75 * (0.5 + 0.5 * sin(uTime * (0.6 + aSeed) + aSeed * 40.0));
            }`,
        fragmentShader: /* glsl */`
            varying float vA;
            void main() {
                float d = length(gl_PointCoord - 0.5);
                float a = smoothstep(0.5, 0.0, d) * vA * 0.7;
                gl_FragColor = vec4(vec3(0.78, 0.75, 1.0), a);
            }`,
    });
    const dust = new THREE.Points(pGeo, pMat);
    world.add(dust);

    /* --- Lampu --- */
    const key = new THREE.PointLight(0x5a43ff, 22, 20);
    key.position.set(-3, 2, 3);
    scene.add(key);
    const rim = new THREE.PointLight(0xd6d1ff, 12, 20);
    rim.position.set(3, -1, 2);
    scene.add(rim);

    /* --- Post-processing --- */
    const composer = new EffectComposer(renderer);
    composer.addPass(new RenderPass(scene, camera));
    const bloom = new UnrealBloomPass(new THREE.Vector2(1, 1), 0.55, 0.6, 0.42);
    composer.addPass(bloom);
    composer.addPass(new OutputPass());

    /* --- Ukuran & layout responsif --- */
    const resize = () => {
        const w = canvas.clientWidth, h = canvas.clientHeight;
        if (!w || !h) return;
        renderer.setSize(w, h, false);
        composer.setSize(w, h);
        bloom.setSize(w, h);
        camera.aspect = w / h;
        // Di layar sempit, mundurkan kamera supaya busur tetap utuh
        camera.position.z = w / h < 0.8 ? 14 : w / h < 1.2 ? 11 : 9;
        world.position.x = w / h > 1.2 ? 0.35 : 0;
        world.position.y = w / h < 0.8 ? 1.2 : 0;
        camera.updateProjectionMatrix();
    };
    new ResizeObserver(resize).observe(canvas);
    resize();

    /* --- Interaksi --- */
    const mouse = { x: 0, y: 0, tx: 0, ty: 0 };
    addEventListener('pointermove', (e) => {
        mouse.tx = (e.clientX / innerWidth) * 2 - 1;
        mouse.ty = (e.clientY / innerHeight) * 2 - 1;
    }, { passive: true });

    let visible = true;
    new IntersectionObserver(([en]) => (visible = en.isIntersecting)).observe(canvas);

    const clock = new THREE.Clock();
    const start = performance.now();
    const render = () => {
        requestAnimationFrame(render);
        if (!visible || document.hidden) return;
        const t = clock.getElapsedTime();
        const p = getProgress();
        arcUniforms.uTime.value = t;
        if (!reduce) {
            const since = (performance.now() - start) / 1000;
            arcUniforms.uReveal.value = Math.min(1, Math.max(0, (since - 1.4) / 1.8));
            arcUniforms.uReveal.value = 1 - Math.pow(1 - arcUniforms.uReveal.value, 3);
        }

        mouse.x += (mouse.tx - mouse.x) * 0.04;
        mouse.y += (mouse.ty - mouse.y) * 0.04;

        const m = reduce ? 0 : 1;
        world.rotation.y = mouse.x * 0.22 * m;
        world.rotation.x = mouse.y * 0.12 * m;
        orb.position.y = -0.15 + Math.sin(t * 0.8) * 0.12 * m;
        orb.position.x = mouse.x * 0.25 * m;
        core.position.set(orb.position.x - mouse.x * 0.15, orb.position.y + mouse.y * 0.1, 0.2);
        halo.position.copy(orb.position);
        halo.rotation.z = t * 0.25 * m;
        ring.rotation.z = t * 0.05 * m;
        ring2.rotation.z = -t * 0.08 * m;
        dust.rotation.y = t * 0.02 * m;
        arc.rotation.y = mouse.x * 0.1 * m;

        // Saat di-scroll jadi "layar monitor": busur sedikit naik, lensa membesar
        arc.position.y = 0.55 + p * 0.4;
        orb.scale.setScalar(1 + p * 0.12);

        composer.render();
    };
    render();
}
