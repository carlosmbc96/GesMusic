<template>
    <!-- Empieza la sección del Footer -->
    <div class="page-footer">
        <div class="page-footer-inner" style="width: 100%; text-align: center!important">
            &copy; Copyright {{new Date().getFullYear()}} | Soluciones Avanzadas de Ingeniería Informática
            <a target="_blank" title="Ir" href="http://saii.cu">SAII</a> &#174; Todos los derechos reservados.
            <div class="scroll-to-top">
                <i class="icon-arrow-up"/>
            </div>
        </div>
    </div>
        <!-- Termina la sección del Footer -->
</template>

<script>
    export default {
        name: "Footer"
    }
</script>

<style>
.page-footer .page-footer-inner {
    color: white !important;
}
</style>
