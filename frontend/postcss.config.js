const cssnano = require("cssnano");
module.exports = {
  // plugins: {
  //   'postcss-flexbugs-fixes': {} ,
  //   'tailwindcss' : {},
  //   'postcss-preset-env' : {
  //     autorpefixer: {
  //
  //     }
  //   }
  // }
  plugins: [
    require("tailwindcss"),
    require("autoprefixer"),
    cssnano({
      preset: "default",
    })
  ],
};
