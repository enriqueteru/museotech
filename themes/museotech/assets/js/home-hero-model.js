// Orientaciones de reposo en radianes: cambia yaw (giro) o pitch (inclinación).
const HOME_EDITORIAL_POSE = Object.freeze({
    repository: Object.freeze({ yaw: 6.5, pitch: -.2 }),
    podcast: Object.freeze({ yaw: 5.6, pitch: 0 }),
});
const STAGE_SELECTOR = '[data-home-hero-model], [data-home-echoes-model], [data-home-podcast-model], [data-home-repository-model], [data-podcast-single-model], [data-podcast-banner-model]';
const CONTENT_SELECTOR = '.home-hero__content, .home-echoes__content, .home-podcast__content, .home-repository__content, .podcast-archive__header, .podcast-single__hero, .news-archive__header, .single-article__lead, .collaborators-page__banner-inner, .collaborator-profile__header, .repository-archive__intro, .repository-single__header';
const clamp = (value, minimum, maximum) => Math.max(minimum, Math.min(maximum, value));

let threeCoreDependencies;
let meshoptDependency;
const loadThreeDependencies = async (needsMeshopt) => {
    if (!threeCoreDependencies) {
        threeCoreDependencies = Promise.all([
            import('three'),
            import('three/addons/loaders/GLTFLoader.js'),
        ]).then(([three, loader]) => ({
            THREE: three,
            GLTFLoader: loader.GLTFLoader,
        }));
    }
    const core = await threeCoreDependencies;
    if (!needsMeshopt) return { ...core, MeshoptDecoder: null };
    meshoptDependency ??= import('three/addons/libs/meshopt_decoder.module.js')
        .then((decoder) => decoder.MeshoptDecoder);
    return { ...core, MeshoptDecoder: await meshoptDependency };
};

const readStageOptions = (stage) => {
    const modelUrl = stage.dataset.modelSrc;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const hardwareConcurrency = Number(navigator.hardwareConcurrency) || 0;
    const deviceMemory = Number(navigator.deviceMemory) || 0;
    const isLowPowerDevice = (hardwareConcurrency > 0 && hardwareConcurrency <= 4)
        || (deviceMemory > 0 && deviceMemory <= 4)
        || window.matchMedia('(pointer: coarse)').matches;
    const isHomeHeroModel = stage.matches('[data-home-hero-model]');
    const isDesktopHomeHero = isHomeHeroModel && window.matchMedia('(min-width: 761px)').matches;
    const isHomeRepositoryModel = stage.matches('[data-home-repository-model]');
    const isHomePodcastModel = stage.matches('[data-home-podcast-model]');
    const isPodcastBannerModel = stage.matches('[data-podcast-banner-model]');
    const isHomeEditorialModel = isHomeRepositoryModel || isHomePodcastModel;
    const shouldAnimateIdle = !reducedMotion && (isHomeEditorialModel || !isLowPowerDevice);
    const homeModelPose = isHomeRepositoryModel
        ? HOME_EDITORIAL_POSE.repository
        : isHomePodcastModel
            ? HOME_EDITORIAL_POSE.podcast
            : null;
    const configuredRotationSpeed = Number.parseFloat(stage.dataset.modelRotationSpeed);
    // Vista desde arriba: Y negativo gira en sentido horario y Y positivo, antihorario.
    const idleRotationSpeed = isHomeEditorialModel
        ? (isHomePodcastModel ? -0.12 : 0.12)
        : Number.isFinite(configuredRotationSpeed)
            ? configuredRotationSpeed
            : isDesktopHomeHero ? 0.045 : 0.11;
    const readRotation = (axis) => {
        const degrees = Number.parseFloat(stage.dataset[`modelRotation${axis.toUpperCase()}`]);
        return Number.isFinite(degrees) ? degrees * (Math.PI / 180) : 0;
    };
    const readPosition = (axis) => {
        const position = Number.parseFloat(stage.dataset[`modelPosition${axis.toUpperCase()}`]);
        return Number.isFinite(position) ? position : 0;
    };

    return {
        modelUrl,
        isLowPowerDevice,
        isHomeHeroModel,
        isHomeRepositoryModel,
        isHomePodcastModel,
        isPodcastBannerModel,
        isHomeEditorialModel,
        shouldAnimateIdle,
        homeModelPose,
        idleRotationSpeed,
        modelRotationX: readRotation('x'),
        modelRotationY: readRotation('y'),
        modelRotationZ: readRotation('z'),
        modelPositionX: readPosition('x'),
        modelPositionY: readPosition('y'),
        modelPositionZ: readPosition('z'),
    };
};

const createInteractionController = (stage, content, shouldAnimateIdle, isReady, renderStatic) => {
    let dragRotationY = 0;
    let dragRotationX = 0;
    let angularVelocityY = 0;
    let angularVelocityX = 0;
    let isDragging = false;
    let activePointerId = null;
    let lastPointerX = 0;
    let lastPointerY = 0;
    let lastPointerAt = 0;

    const endDrag = (event) => {
        if (event.pointerId !== activePointerId) return;

        isDragging = false;
        activePointerId = null;
        stage.classList.remove('is-dragging');
        content.classList.remove('is-model-dragging');
        if (content.hasPointerCapture(event.pointerId)) content.releasePointerCapture(event.pointerId);
    };

    content.addEventListener('pointerdown', (event) => {
        if (event.target.closest('a, button, input, textarea, select, [contenteditable="true"]')) return;

        const bounds = stage.getBoundingClientRect();
        const insideModel = event.clientX >= bounds.left && event.clientX <= bounds.right && event.clientY >= bounds.top && event.clientY <= bounds.bottom;
        if (!isReady() || activePointerId !== null || !insideModel || (event.pointerType === 'mouse' && event.button !== 0)) return;

        isDragging = true;
        activePointerId = event.pointerId;
        lastPointerX = event.clientX;
        lastPointerY = event.clientY;
        lastPointerAt = event.timeStamp || performance.now();
        angularVelocityY = 0;
        angularVelocityX = 0;
        stage.classList.add('is-dragging');
        content.classList.add('is-model-dragging');
        content.setPointerCapture(event.pointerId);
        event.preventDefault();
    });

    content.addEventListener('pointermove', (event) => {
        if (!isDragging || event.pointerId !== activePointerId) return;

        const deltaX = event.clientX - lastPointerX;
        const now = event.timeStamp || performance.now();
        const elapsed = Math.max((now - lastPointerAt) * 0.001, 1 / 120);
        const rotationY = deltaX * 0.012;
        dragRotationY += rotationY;

        const rotationX = (event.clientY - lastPointerY) * 0.008;
        dragRotationX = clamp(dragRotationX + rotationX, -0.55, 0.55);
        angularVelocityY = clamp(angularVelocityY * 0.25 + clamp(rotationY / elapsed, -4, 4) * 0.75, -4, 4);
        angularVelocityX = clamp(angularVelocityX * 0.25 + clamp(rotationX / elapsed, -3, 3) * 0.75, -3, 3);
        lastPointerX = event.clientX;
        lastPointerY = event.clientY;
        lastPointerAt = now;

        if (!shouldAnimateIdle) renderStatic();
    });

    content.addEventListener('pointerup', endDrag);
    content.addEventListener('pointercancel', endDrag);

    stage.addEventListener('keydown', (event) => {
        if (!isReady()) return;

        const rotationStep = 0.12;
        if (event.key === 'ArrowLeft') dragRotationY -= rotationStep;
        else if (event.key === 'ArrowRight') dragRotationY += rotationStep;
        else if (event.key === 'ArrowUp') dragRotationX = Math.max(-0.55, dragRotationX - 0.08);
        else if (event.key === 'ArrowDown') dragRotationX = Math.min(0.55, dragRotationX + 0.08);
        else return;

        event.preventDefault();
        if (!shouldAnimateIdle) renderStatic();
    });

    return {
        get isDragging() { return isDragging; },
        get rotationX() { return dragRotationX; },
        get rotationY() { return dragRotationY; },
        advance(delta) {
            dragRotationY += angularVelocityY * delta;
            const nextPitch = dragRotationX + angularVelocityX * delta;
            dragRotationX = clamp(nextPitch, -0.55, 0.55);
            if (nextPitch !== dragRotationX) angularVelocityX = 0;

            const friction = Math.exp(-1.8 * delta);
            angularVelocityY *= friction;
            angularVelocityX *= friction;
            if (Math.abs(angularVelocityY) < 0.01) angularVelocityY = 0;
            if (Math.abs(angularVelocityX) < 0.01) angularVelocityX = 0;
        },
    };
};

const createRenderer = (THREE, isLowPowerDevice, isEditorialModel) => new THREE.WebGLRenderer({
    alpha: true,
    antialias: isEditorialModel || !isLowPowerDevice,
    powerPreference: 'low-power',
});

const configureRenderer = (THREE, renderer, isLowPowerDevice, isEditorialModel) => {
    const isMobileEditorialModel = isEditorialModel && window.matchMedia('(max-width: 760px)').matches;
    const pixelRatioLimit = isMobileEditorialModel ? 1.75 : isLowPowerDevice ? 1 : 1.15;
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, pixelRatioLimit));
    renderer.setClearColor(0x000000, 0);
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.15;
    renderer.domElement.setAttribute('aria-hidden', 'true');
};

const createScene = (THREE, options) => {
    const {
        isHomeHeroModel,
        isHomePodcastModel,
        homeModelPose,
        modelRotationX,
        modelRotationY,
        modelRotationZ,
        modelPositionX,
        modelPositionY,
        modelPositionZ,
    } = options;
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(32, 1, 0.1, 30);
    const cameraSide = isHomePodcastModel ? -2.55 : 2.55;
    if (isHomeHeroModel) camera.position.set(1.15, 0.7, 4.35);
    else camera.position.set(cameraSide, 1.65, 3.55);
    camera.lookAt(0, 0, 0);

    scene.add(new THREE.HemisphereLight(0xfff7eb, 0x342a3b, 2.1));

    const keyLight = new THREE.DirectionalLight(0xffffff, 3.2);
    keyLight.position.set(isHomePodcastModel ? 3 : -3, 4, 4);
    scene.add(keyLight);

    const rimLight = new THREE.DirectionalLight(0xa9d1d8, 2.1);
    rimLight.position.set(isHomePodcastModel ? -3 : 3, 2, -4);
    scene.add(rimLight);

    const pivot = new THREE.Group();
    pivot.rotation.set(modelRotationX, (homeModelPose?.yaw ?? -0.38) + modelRotationY, modelRotationZ);
    pivot.position.set(modelPositionX, modelPositionY, modelPositionZ);
    scene.add(pivot);
    return { scene, camera, pivot };
};

const fitCameraToModel = (THREE, camera, pivot, options) => {
    const { isPodcastBannerModel, isHomePodcastModel, isHomeRepositoryModel } = options;
    let sceneRadius = 0;
    pivot.updateMatrixWorld(true);
    pivot.traverse((object) => {
        if (!object.isMesh && !object.isPoints) return;
        const geometry = object.geometry;
        if (!geometry.boundingSphere) geometry.computeBoundingSphere();
        const worldBounds = geometry.boundingSphere.clone().applyMatrix4(object.matrixWorld);
        sceneRadius = Math.max(sceneRadius, worldBounds.center.length() + worldBounds.radius);
    });

    const limitingHalfFov = Math.atan(
        Math.tan(THREE.MathUtils.degToRad(camera.fov / 2)) * Math.min(camera.aspect, 1),
    );
    const fitMargin = isPodcastBannerModel
        ? 1.3
        : isHomePodcastModel
            ? 1.08
            : isHomeRepositoryModel
                ? 1.08
                : 1.12;
    const fitDistance = (sceneRadius / Math.sin(limitingHalfFov)) * fitMargin;
    const closestCameraDistance = fitDistance / 0.96;
    return camera.position.clone().normalize().multiplyScalar(
        Math.max(camera.position.length(), closestCameraDistance),
    );
};

const lightenModelTextures = (THREE, model) => {
    let adjustedTextures = 0;
    const lightenColorTexture = (texture) => {
        const image = texture?.image;
        if (!image?.width || !image?.height) return texture;

        const canvas = document.createElement('canvas');
        canvas.width = image.width;
        canvas.height = image.height;
        const context = canvas.getContext('2d');
        if (!context) return texture;

        context.filter = 'brightness(1.15) saturate(0.86)';
        context.drawImage(image, 0, 0, canvas.width, canvas.height);

        const lightened = new THREE.CanvasTexture(canvas);
        adjustedTextures += 1;
        lightened.colorSpace = texture.colorSpace;
        lightened.mapping = texture.mapping;
        lightened.channel = texture.channel;
        lightened.wrapS = texture.wrapS;
        lightened.wrapT = texture.wrapT;
        lightened.magFilter = texture.magFilter;
        lightened.minFilter = texture.minFilter;
        lightened.anisotropy = texture.anisotropy;
        lightened.flipY = texture.flipY;
        lightened.offset.copy(texture.offset);
        lightened.repeat.copy(texture.repeat);
        lightened.center.copy(texture.center);
        lightened.rotation = texture.rotation;
        lightened.matrixAutoUpdate = texture.matrixAutoUpdate;
        if (!texture.matrixAutoUpdate) lightened.matrix.copy(texture.matrix);

        return lightened;
    };

    model.traverse((object) => {
        if (!object.isMesh) return;

        const lightenMaterial = (material) => {
            if (!material.map) return material;
            const updated = material.clone();
            updated.map = lightenColorTexture(material.map);
            return updated;
        };

        object.material = Array.isArray(object.material)
            ? object.material.map(lightenMaterial)
            : lightenMaterial(object.material);
    });
    return adjustedTextures;
};

const addNormalizedModel = (THREE, model, pivot, isHomeEditorialModel) => {
    const bounds = new THREE.Box3().setFromObject(model);
    const center = bounds.getCenter(new THREE.Vector3());
    const size = bounds.getSize(new THREE.Vector3());
    const scale = 1.75 / Math.max(size.x, size.y, size.z);

    model.position.sub(center).multiplyScalar(scale);
    model.scale.setScalar(scale);
    if (isHomeEditorialModel) model.scale.multiplyScalar(1.62);
    pivot.add(model);
};

const mountModelStage = (stage) => {
    const content = stage.closest(CONTENT_SELECTOR);
    if (!content) return;
    const options = readStageOptions(stage);
    const {
        modelUrl,
        isLowPowerDevice,
        isHomeHeroModel,
        isPodcastBannerModel,
        isHomeEditorialModel,
        shouldAnimateIdle,
        homeModelPose,
        idleRotationSpeed,
        modelRotationX,
        modelRotationY,
        modelRotationZ,
        modelPositionX,
        modelPositionY,
        modelPositionZ,
    } = options;

    let renderer;
    let THREE;
    let GLTFLoader;
    let MeshoptDecoder;
    let camera;
    let scene;
    let pivot;
    let generalCameraPosition;
    let animationFrame = 0;
    let lastRenderedAt = 0;
    let lastPoseAt = 0;
    let isVisible = false;
    let hasLoaded = false;
    let modelScale = 1;
    let autoRotation = 0;
    let pendingInitialize = null;
    let pendingInitializeIsIdle = false;

    const interaction = isHomeEditorialModel
        ? { isDragging: false, rotationX: 0, rotationY: 0, advance() {} }
        : createInteractionController(
            stage,
            content,
            shouldAnimateIdle,
            () => Boolean(pivot),
            () => renderPose(performance.now()),
        );

    const renderPose = (timestamp) => {
        if (!pivot || !renderer || !camera) return;

        const delta = lastPoseAt ? Math.min((timestamp - lastPoseAt) * 0.001, 0.06) : 0;
        lastPoseAt = timestamp;
        const elapsed = timestamp * 0.001;

        if (shouldAnimateIdle && !interaction.isDragging) {
            interaction.advance(delta);
            autoRotation += delta * idleRotationSpeed;
        }

        const baseYaw = (homeModelPose?.yaw ?? (isHomeHeroModel ? -0.18 : -0.38)) + modelRotationY;
        const basePitch = (homeModelPose?.pitch ?? 0) + modelRotationX;
        pivot.rotation.y = baseYaw + interaction.rotationY + autoRotation;
        const pitchMotion = isHomeEditorialModel ? 0 : interaction.rotationX + (shouldAnimateIdle ? Math.sin(elapsed * 0.7) * 0.012 : 0);
        pivot.rotation.x = basePitch + pitchMotion;
        pivot.rotation.z = modelRotationZ;
        pivot.position.set(
            modelPositionX,
            modelPositionY + (shouldAnimateIdle && !isHomeEditorialModel ? Math.sin(elapsed * 0.8) * 0.025 : 0),
            modelPositionZ,
        );
        pivot.scale.setScalar(modelScale);
        renderer.render(scene, camera);
    };

    const resize = () => {
        if (!renderer || !camera) return;

        const width = Math.max(1, stage.clientWidth);
        const height = Math.max(1, stage.clientHeight);
        renderer.setSize(width, height, false);
        camera.aspect = width / height;
        camera.updateProjectionMatrix();

        if (!shouldAnimateIdle && pivot) renderPose(performance.now());
    };

    const render = (timestamp) => {
        animationFrame = 0;
        if (!isVisible || document.hidden || !pivot || !renderer) return;

        if (timestamp - lastRenderedAt < 1000 / 30) {
            animationFrame = window.requestAnimationFrame(render);
            return;
        }
        lastRenderedAt = timestamp;

        renderPose(timestamp);
        animationFrame = window.requestAnimationFrame(render);
    };

    const startRendering = () => {
        if (!renderer || !pivot || !isVisible || document.hidden) return;

        if (!shouldAnimateIdle) {
            renderPose(performance.now());
            return;
        }

        if (!animationFrame) animationFrame = window.requestAnimationFrame(render);
    };

    const setStaticPose = () => {
        const configuredScale = Number.parseFloat(stage.dataset.modelScale);
        modelScale = Number.isFinite(configuredScale) && configuredScale > 0
            ? configuredScale
            : stage.matches('[data-home-repository-model]') ? 1 : 1.15;
    };

    const updateEditorialCamera = () => {
        if (!camera || !generalCameraPosition) return;

        camera.position.copy(generalCameraPosition);
        camera.lookAt(0, 0, 0);
        if (!shouldAnimateIdle) renderPose(performance.now());
    };

    const initialize = async () => {
        if (hasLoaded || !modelUrl) return;
        hasLoaded = true;

        try {
            ({ THREE, GLTFLoader, MeshoptDecoder } = await loadThreeDependencies(stage.dataset.modelMeshopt === 'true'));
            renderer = createRenderer(THREE, isLowPowerDevice, options.isHomeEditorialModel);
        } catch (error) {
            stage.dataset.modelState = 'unsupported';
            return;
        }

        configureRenderer(THREE, renderer, isLowPowerDevice, options.isHomeEditorialModel);
        ({ scene, camera, pivot } = createScene(THREE, options));

        const resizeObserver = new ResizeObserver(resize);
        resizeObserver.observe(stage);
        resize();

        const loader = new GLTFLoader();
        if (MeshoptDecoder) loader.setMeshoptDecoder(MeshoptDecoder);
        loader.load(modelUrl, (gltf) => {
            const model = gltf.scene;
            stage.dataset.textureState = `lightened-${lightenModelTextures(THREE, model)}`;
            addNormalizedModel(THREE, model, pivot, isHomeEditorialModel);
            setStaticPose();
            pivot.scale.setScalar(modelScale);
            if (isHomeEditorialModel || isPodcastBannerModel) {
                generalCameraPosition = fitCameraToModel(THREE, camera, pivot, options);
                updateEditorialCamera();
            }
            stage.appendChild(renderer.domElement);
            stage.classList.add('is-ready');
            stage.dataset.modelState = 'ready';
            startRendering();
        }, undefined, () => {
            stage.dataset.modelState = 'error';
        });
    };

    const scheduleInitialize = () => {
        if (!isVisible || hasLoaded || pendingInitialize !== null) return;

        const start = () => {
            pendingInitialize = null;
            if (isVisible) initialize();
        };

        if ('requestIdleCallback' in window) {
            pendingInitializeIsIdle = true;
            pendingInitialize = window.requestIdleCallback(start, { timeout: 350 });
        } else {
            pendingInitializeIsIdle = false;
            pendingInitialize = window.setTimeout(start, 100);
        }
    };

    const cancelScheduledInitialize = () => {
        if (pendingInitialize === null) return;
        if (pendingInitializeIsIdle && 'cancelIdleCallback' in window) {
            window.cancelIdleCallback(pendingInitialize);
        } else {
            window.clearTimeout(pendingInitialize);
        }
        pendingInitialize = null;
    };

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            window.cancelAnimationFrame(animationFrame);
            animationFrame = 0;
        } else {
            startRendering();
        }
    });

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(([entry]) => {
            isVisible = entry.isIntersecting;
            if (isVisible) scheduleInitialize();
            if (isVisible) startRendering();
            else {
                cancelScheduledInitialize();
                window.cancelAnimationFrame(animationFrame);
                animationFrame = 0;
            }
        }, { rootMargin: '120px' });
        observer.observe(stage);
    } else {
        isVisible = true;
        scheduleInitialize();
    }
};

document.querySelectorAll(STAGE_SELECTOR).forEach(mountModelStage);
