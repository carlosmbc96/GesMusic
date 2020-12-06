<template>
  <div>
    <!-- Comienza la sección del formulario -->
    <a-col id="login" span="6" style="border-radius: 8px">
      <div style="text-align: center; padding: 25px; margin-top: -3%">
        <img
          style="width: 50%"
          src="../../../public/assets/images/Logo ver vertical_Ltr Blancas.png"
        />
        <br />
        <h3>Ingrese su cuenta</h3>
        <a-form-model :model="user" @submit="login" @submit.native.prevent>
          <a-form-model-item prop="username">
            <a-input v-model="user.username" placeholder="Nombre de usuario">
              <a-icon slot="prefix" type="user" />
            </a-input>
          </a-form-model-item>
          <a-form-model-item prop="password" style="margin-top: -5%">
            <a-input
              v-model="user.password"
              type="password"
              placeholder="Contraseña"
            >
              <a-icon slot="prefix" type="lock" />
            </a-input>
          </a-form-model-item>
          <a-form-model-item style="margin-top: -5%">
            <a-button html-type="submit" style="border-color: rgb(255,255,255,0.8) !important" > Autenticarse </a-button>
          </a-form-model-item>
        </a-form-model>
        <a-checkbox> Recuérdame </a-checkbox>
        <br /><br />
        <h4>¿Olvidó su contraseña?</h4>
        <p>
          no se preocupe, haga clic <a href="">aquí</a> para restablecer su
          contraseña
        </p>
        <hr />
        <p>¿Aún no tienes cuenta? <a href="">Crea una cuenta</a></p>
      </div>
    </a-col>
    <!-- Termina la sección del formulario -->
    <!-- Comienza el footer -->
    <a-col span="8" style="margin-left: 32%">
      <br />
      <p style="color: rgba(255, 255, 255, 0.8); text-align: center">
        @Copyright 2021 Soluciones Avanzadas de Ingeniería Informática SAII
        ©Todos los derechos reservados
      </p>
    </a-col>
    <!-- Termina el footer -->
  </div>
</template>

<script>
export default {
  name: "Login",
  data() {
    return {
      user: {
        username: "",
        password: "",
      },
    };
  },
  methods: {
    login(e) {
      if (this.user.username === "admin" && this.user.password === "admin") {
        return this.$emit("loged", true);
      }
    },
  },
};
</script>

<style>
#login {
  margin-top: 5% !important;
  margin-left: 36% !important;
  background-color: rgba(255, 255, 255, 0.4) !important;
}
#login input,
#login button {
  background-color: transparent !important;
}
#login p,
#login h4,
#login h3,
#login span {
  color: rgba(255, 255, 255, 0.8) !important;
}
#login a {
  color: rgb(115, 25, 84) !important;
}
#login ::placeholder {
  color: rgb(39, 36, 36) !important;
}
</style>