<template>
  <div>
    <topbar />
    <sidebar @change_route="router = $event" />
    <div v-if="content">
      <div class="page-content-wrapper" style="opacity: 1">
        <div
          class="page-content"
          style="background-color: rgba(255, 255, 255, 0)"
        >
          <div
            class="profile-content"
            style="background-color: rgba(255, 255, 255, 0)"
          >
            <div class="col-md-12">
              <div
                class="portlet light"
                style="background-color: rgba(255, 255, 255, 0.4)"
              >
                <router-view />
              </div>
            </div>
          </div>
          <footertemplate />
        </div>
      </div>
    </div>
    <div v-else>
      <footertemplate />
    </div>
  </div>
</template>

<script>
import Vue from "vue";
import topbar from "./layout/Top_Bar";
import footertemplate from "./layout/Footer";
import sidebar from "./layout/Side_Bar";

export default {
  name: "Home",
  components: {
    topbar,
    footertemplate,
    sidebar,
  },
  data() {
    return {
      router: this.$router.currentRoute.fullPath,
      content: this.$router.currentRoute.fullPath === "/" ? false : true,
    };
  },
  beforeDestroy() {
    const recaptchaScripts = document.querySelectorAll("script");
    for (let i = 0; i < recaptchaScripts.length; i += 1) {
      recaptchaScripts[i].parentElement.removeChild(recaptchaScripts[i]);
    }
  },
  mounted() {
    Vue.loadScript("../assets/global/plugins/jquery.min.js").then(() => {
      Vue.loadScript(
        "../assets/global/plugins/bootstrap/js/bootstrap.min.js"
      ).then(() => {
        Vue.loadScript("../assets/global/plugins/js.cookie.min.js").then(() => {
          Vue.loadScript(
            "../assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js"
          ).then(() => {
            Vue.loadScript(
              "../assets/global/plugins/jquery.blockui.min.js"
            ).then(() => {
              Vue.loadScript(
                "../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js"
              ).then(() => {
                Vue.loadScript("../assets/global/scripts/app.min.js").then(
                  () => {
                    Vue.loadScript(
                      "../assets/layouts/layout2/scripts/layout.min.js"
                    ).then(() => {
                      Vue.loadScript(
                        "../assets/layouts/layout2/scripts/demo.min.js"
                      ).then(() => {
                        Vue.loadScript(
                          "../assets/layouts/global/scripts/quick-sidebar.min.js"
                        ).then(() => {
                          Vue.loadScript(
                            "../assets/layouts/global/scripts/quick-nav.min.js"
                          ).then(() => {
                            Vue.loadScript("../js/main.js").then(() => {
                              console.log("Scripts loaded");
                            });
                          });
                        });
                      });
                    });
                  }
                );
              });
            });
          });
        });
      });
    });
  },
  watch: {
    router: function () {
      return this.router === "/"
        ? (this.content = false)
        : (this.content = true);
    },
  },
};
</script>

<style>
.e-badge.e-badge-success:not(.e-badge-ghost):not([href]),
.e-badge.e-badge-success[href]:not(.e-badge-ghost) {
  background-color: rgb(76, 196, 177) !important;
  color: black !important;
}
.e-badge.e-badge-warning:not(.e-badge-ghost):not([href]),
.e-badge.e-badge-warning[href]:not(.e-badge-ghost) {
  background-color: rgb(243, 107, 100) !important;
  color: white !important;
}
.e-badge.e-badge-warning:not(.e-badge-ghost):not([href]),
.e-badge.e-badge-warning[href]:not(.e-badge-ghost) {
  background-color: rgb(243, 107, 100) !important;
  color: white !important;
}
.e-commands-custom {
  background-color: transparent !important;
  box-shadow: none !important;
}
.e-eye-icon:before {
  content: "\e345";
  color: rgb(115, 25, 84) !important;
  font-size: 18px !important;
}
.e-edit-icon:before {
  content: "\e7a3" !important;
  color: rgb(115, 25, 84) !important;
  font-size: 18px !important;
}
.e-add-icon:before {
  content: "\e22c" !important;
  color: rgb(115, 25, 84) !important;
  font-size: 18px !important;
}
.e-print-export:before {
  content: "\e813" !important;
  color: rgb(115, 25, 84) !important;
  font-size: 18px !important;
}
.e-pdf-export:before {
  content: "\e240" !important;
  color: rgb(115, 25, 84) !important;
  font-size: 18px !important;
}
.e-excel-export:before {
  content: "\e242" !important;
  color: rgb(115, 25, 84) !important;
  font-size: 18px !important;
}
.e-csv-export:before {
  content: "\e241" !important;
  color: rgb(115, 25, 84) !important;
  font-size: 18px !important;
}
.e-tbar-btn-text {
  font-weight: bold;
}
.e-btn {
  background: transparent !important;
  border-color: transparent !important;
}
.e-unboundcelldiv .e-commands-custom {
  background: transparent !important;
  border: none !important;
}
.inactive_row {
  color: rgb(243, 107, 100) !important;
}
.iziToast-color-green {
  background-color: #70d0c1 !important;
}
.iziToast-color-blue {
  background-color: #8f4776 !important;
}
.iziToast-color-grey {
  background-color: #c9d34d !important;
}
.iziToast-color-orange {
  background-color: #f3c552 !important;
}
#question .iziToast-message.slideIn {
  margin-top: -7px !important;
}
.iziToast {
  border-radius: 5px !important;
}
.iziToast-progressbar {
  width: 99% !important;
}
.iziToast-color-red {
  background-color: #f58983 !important;
}
.page-sidebar .page-sidebar-menu > li.open > a,
.page-sidebar .page-sidebar-menu > li:hover > a,
.page-sidebar-closed.page-sidebar-fixed
  .page-sidebar:hover
  .page-sidebar-menu
  > li.open
  > a,
.page-sidebar-closed.page-sidebar-fixed
  .page-sidebar:hover
  .page-sidebar-menu
  > li:hover
  > a {
  background: rgba(255, 255, 255, 0.2) !important;
  color: #e4e9f2;
}

.section-title {
  border-bottom: 1px solid #0000001f !important;
  margin-bottom: 10px;
}
.nav-link:hover,
.page-sidebar
  .page-sidebar-menu.page-sidebar-menu-compact
  > li
  > .sub-menu
  > li
  > a:hover,
.page-sidebar
  .page-sidebar-menu.page-sidebar-menu-compact
  > li.open
  > .nav-link {
  background-color: rgba(255, 255, 255, 0.2) !important;
}
.cancel-text,
.cancel-text:hover {
  background-color: rgb(243, 107, 100) !important;
  border: 1px solid rgb(243, 107, 100);
  color: white;
}
.ant-btn,
.ant-btn:hover {
  border-color: transparent !important;
}
.ant-modal-content {
  background-color: rgba(255, 255, 255, 0.85) !important;
}
.ant-modal-footer {
  background-color: rgba(0, 0, 0, 0.2) !important;
}
.ant-form-explain {
  color: rgb(243, 107, 100) !important;
}

.has-error .ant-input {
  border-color: rgb(243, 107, 100) !important;
}
.ant-btn-danger {
  background-color: rgb(243, 107, 100) !important;
  border-color: rgb(243, 107, 100) !important;
}
.has-error .ant-select-selection {
  border-color: rgb(243, 107, 100) !important;
}

.has-error.has-feedback .ant-form-item-children-icon {
  color: rgb(243, 107, 100) !important;
}

.has-success.has-feedback .ant-form-item-children-icon {
  color: rgb(76, 196, 177) !important;
}

.ant-form-item-required::before {
  color: rgb(243, 107, 100) !important;
}
.component-fade-enter-active,
.component-fade-leave-active {
  transition: opacity 0.6s ease;
}
.component-fade-enter,
.component-fade-leave-to {
  opacity: 0;
}
.ant-tabs-ink-bar {
  background-color: transparent !important;
  position: initial !important;
}
.ant-tabs-nav {
  float: right !important;
}
.ant-tabs-extra-content {
  float: left !important;
  color: rgba(0, 0, 0, 0.75);
  font-weight: 500;
  font-size: 20px;
}
.ant-tabs-bar,
.ant-tabs-top-bar {
  margin-bottom: 1% !important;
}
.ant-tabs-ink-bar {
  background-color: white !important;
}
.ant-tabs-extra-content {
  color: rgb(115, 25, 84) !important;
  font-size: 3rem !important;
}
.ant-tabs-nav .ant-tabs-tab-disabled,
.ant-tabs-nav .ant-tabs-tab-active,
.ant-tabs-nav .ant-tabs-tab {
  color: rgb(46, 171, 229) !important;
}
.ant-tabs-nav .ant-tabs-tab {
  padding: 12px 0px !important;
}
.ant-tabs-bar {
  border-bottom: 1px solid rgb(115, 25, 84) !important;
}
.anticon-close-square {
  background-color: white !important;
  color: rgb(243, 107, 100) !important;
}
.ant-btn-default,
.ant-btn-default:hover {
  border-color: rgb(76, 196, 177) !important;
  background-color: rgb(76, 196, 177) !important;
  color: white !important;
}
.ant-btn-primary,
.ant-switch-checked {
  background-color: rgb(46, 171, 229) !important;
  border-color: rgb(46, 171, 229) !important;
}
.ant-message-success .anticon {
  color: rgb(76, 196, 177) !important;
}
.ant-message-error .anticon {
  color: rgb(243, 107, 100) !important;
}
.ant-switch-inner {
  width: 50px !important;
}
.ant-modal-root.ant-modal-confirm.ant-modal-confirm-info.ant-modal-info.ant-modal-confirm.ant-modal-confirm-info.ant-modal-info
  .ant-modal {
  margin-left: auto !important;
  margin-top: auto !important;
}
.ant-modal-root.ant-modal-confirm.ant-modal-confirm-info.ant-modal-info.ant-modal-confirm.ant-modal-confirm-info.ant-modal-info
  .ant-modal-content {
  background-color: white !important;
}
.ant-modal {
  margin-left: 17% !important;
  margin-top: -31px !important;
}
.has-success input,
.has-success textarea,
.has-success .ant-select-selection {
  border-color: rgb(76, 196, 177) !important;
  outline: 0 none !important;
  border: 1px solid rgb(76, 196, 177) !important;
}
.has-success input[type="text"]:focus,
.has-success textarea:focus,
.has-success .ant-select-selection:focus {
  border-color: rgb(76, 196, 177) !important;
  box-shadow: 0 0 0 rgb(76, 196, 177) inset, 0 0 3px rgb(76, 196, 177) !important;
  outline: 0 none !important;
}
.ant-select-search input {
  border-color: rgb(255, 255, 255) !important;
}
.e-icon-export {
  color: rgb(115, 25, 84) !important;
  font-size: 20px !important;
}
.e-down-arrow-export:before {
  content: "\e652" !important;
  color: rgb(115, 25, 84) !important;
  font-size: 14px !important;
  cursor: pointer;
}
.e-up-arrow-export:before {
  content: "\e651" !important;
  color: rgb(115, 25, 84) !important;
  font-size: 14px !important;
  cursor: pointer;
}
.export-icons {
  margin-right: 6%;
  cursor: pointer;
}
.export-icons:hover {
  text-shadow: 1px 1px 2px rgba(115, 25, 84, 0.7);
  transition: 0.5s;
}
#exportPanelContainer {
  float: right;
  width: 20%;
  background: transparent;
  overflow-y: hidden;
}
#dropUpExports {
  width: 70%;
  height: 40px !important;
  background-color: rgba(255, 255, 255, 0.8) !important;
  padding: 10px;
  border-radius: 5px 0 0 0;
}
#arrowDropUpExports {
  width: 30%;
  height: 40px !important;
  float: right;
  background-color: rgba(255, 255, 255, 0.8) !important;
  padding: 10px;
  border-radius: 0 5px 0 0;
}
.e-delete-physical-icon:before {
  content: "\e94a" !important;
  color: black !important;
  font-size: 15px !important;
}
.e-delete-logical-icon:before {
  content: "\ec1c" !important;
  color: black !important;
  font-size: 15px !important;
}
.e-grid {
  border-radius: 5px 0 5px 5px !important;
}
.ant-form-item-label {
  margin-bottom: -15px !important;
}
.iziToast-buttons button:focus {
  outline: none !important;
  box-shadow: none !important;
}
.page-sidebar
  .page-sidebar-menu.page-sidebar-menu-compact
  > li
  > .sub-menu
  > li
  > a,
.page-sidebar-closed.page-sidebar-fixed
  .page-sidebar:hover
  .page-sidebar-menu.page-sidebar-menu-compact
  > li
  > .sub-menu
  > li
  > a {
  padding-left: 22px;
}
.has-error .ant-input-group-addon {
  color: currentColor !important;
  background-color: #fafafa !important;
  border-top: 1px solid #d9d9d9 !important;
  border-bottom: 1px solid #d9d9d9 !important;
  border-left: 1px solid #d9d9d9 !important;
}
.page-sidebar .page-sidebar-menu .sub-menu > li.active > a,
.page-sidebar .page-sidebar-menu .sub-menu > li.open > a,
.page-sidebar .page-sidebar-menu .sub-menu > li:hover > a,
.page-sidebar-closed.page-sidebar-fixed
  .page-sidebar:hover
  .page-sidebar-menu
  .sub-menu
  > li.active
  > a,
.page-sidebar-closed.page-sidebar-fixed
  .page-sidebar:hover
  .page-sidebar-menu
  .sub-menu
  > li.open
  > a,
.page-sidebar-closed.page-sidebar-fixed
  .page-sidebar:hover
  .page-sidebar-menu
  .sub-menu
  > li:hover
  > a {
  background: rgba(255, 255, 255, 0.2) !important;
}
.page-sidebar-closed .page-sidebar .page-sidebar-menu:hover .sub-menu,
.page-sidebar-closed
  .page-sidebar-closed.page-sidebar-fixed
  .page-sidebar:hover
  .page-sidebar-menu:hover
  .sub-menu {
  background: rgb(115, 25, 84, 0.2) !important;
}
.ant-popover-placement-left .ant-popover-inner {
  background-color: #f9e2a9e0 !important;
}
.ant-popover-placement-top .ant-popover-inner {
  background-color: #c7a3bbe0 !important;
}
.ant-popover-placement-leftBottom .ant-popover-inner {
  background-color: #fac4c1e0 !important;
}
.ant-popover-placement-leftBottom .ant-popover-arrow {
  border-top-color: #fac4c1 !important;
  border-right-color: #fac4c1 !important;
  border-bottom-color: transparent !important;
  border-left-color: transparent !important;
}
.ant-popover-placement-top .ant-popover-arrow {
  border-top-color: transparent !important;
  border-right-color: #c7a3bb !important;
  border-bottom-color: #c7a3bb !important;
  border-left-color: transparent !important;
}
.ant-popover-placement-bottom .ant-popover-inner {
  background-color: #e4e9a6e0 !important;
}
.ant-popover-placement-bottom .ant-popover-arrow {
  border-top-color: #e4e9a6 !important;
  border-right-color: transparent !important;
  border-bottom-color: transparent !important;
  border-left-color: #e4e9a6 !important;
}
.ant-popover-placement-left .ant-popover-arrow {
  border-top-color: #f9e2a9 !important;
  border-right-color: #f9e2a9 !important;
  border-bottom-color: transparent !important;
  border-left-color: transparent !important;
}
.ant-popover-placement-topRight .ant-popover-inner {
  background-color: #b7e7e0e0 !important;
}
.ant-popover-placement-topRight .ant-popover-arrow {
  border-top-color: transparent !important;
  border-right-color: #b7e7e0 !important;
  border-bottom-color: #b7e7e0 !important;
  border-left-color: transparent !important;
}
.ant-popover-buttons > button {
  background-color: rgb(243, 107, 100) !important;
  color: white !important;
}
.ant-popover-buttons > button.ant-btn-primary {
  background-color: rgb(46, 171, 229) !important;
  color: white !important;
}
.ant-popover-message > .anticon {
  position: absolute !important;
  top: 5px !important;
  font-size: 20px !important;
}
.ant-message-custom-content .ant-message-success {
  background: #b7e7e0e0;
}
.e-control.e-grid .e-gridheader .e-sortfilter .e-fltr-icon .e-headercelldiv {
  margin: -7px 1px -7px -7px !important;
}
.ant-spin-nested-loading > div > .ant-spin {
  position: inherit !important;
}
#dropUpExports {
  padding-left: 10px !important;
  padding-top: 10px !important;
  padding: 0px;
}
#arrowDropUpExports {
  padding-top: 10px !important;
  padding: 0px;
}
.e-icons.export-icons.e-up-arrow-export {
  margin-left: 8px !important;
}
.e-icons.export-icons.e-down-arrow-export {
  margin-left: 8px !important;
}
.ant-steps {
  width: 50% !important;
}
.ant-steps-item-process .ant-steps-item-icon {
  background: rgba(115, 25, 84) !important;
  border-color: rgba(115, 25, 84) !important;
}
.ant-steps-item-finish .ant-steps-item-icon {
  background-color: white !important;
  border-color: rgba(115, 25, 84) !important;
}
.ant-steps-item-finish .ant-steps-item-icon > .ant-steps-icon {
  color: rgba(115, 25, 84) !important;
}
.ant-steps-item-finish
  > .ant-steps-item-container
  > .ant-steps-item-content
  > .ant-steps-item-title::after {
  background-color: rgba(115, 25, 84) !important;
}
.ant-steps-item-process
  > .ant-steps-item-container
  > .ant-steps-item-content
  > .ant-steps-item-title::after {
  background-color: #0000001f !important;
}
.ant-steps
  .ant-steps-item:not(.ant-steps-item-active):not(.ant-steps-item-process)
  > .ant-steps-item-container[role="button"]:hover
  .ant-steps-item-icon {
  border-color: rgba(115, 25, 84) !important;
}
.ant-steps
  .ant-steps-item:not(.ant-steps-item-active)
  > .ant-steps-item-container[role="button"]:hover
  .ant-steps-item-title,
.ant-steps
  .ant-steps-item:not(.ant-steps-item-active)
  > .ant-steps-item-container[role="button"]:hover
  .ant-steps-item-subtitle,
.ant-steps
  .ant-steps-item:not(.ant-steps-item-active)
  > .ant-steps-item-container[role="button"]:hover
  .ant-steps-item-description {
  color: rgba(115, 25, 84) !important;
}
.ant-steps
  .ant-steps-item:not(.ant-steps-item-active):not(.ant-steps-item-process)
  > .ant-steps-item-container[role="button"]:hover
  .ant-steps-item-icon
  .ant-steps-icon {
  color: rgba(115, 25, 84) !important;
}
.ant-spin-dot-item {
  background-color: rgba(115, 25, 84) !important;
}
.detalles-img
  .ant-upload-list-picture-card
  .ant-upload-list-item-actions
  .anticon-delete {
  display: none !important;
}
.e-spinner-pane .e-spin-hide {
  display: none !important;
}
#desciption-problem-icon .ant-form-item-children-icon {
  margin-top: -19px !important;
}
</style>
