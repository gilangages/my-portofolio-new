// Style Pink & Purple
const styles = {
  title: "color: #EC4899; font-size: 20px; font-weight: bold; font-family: sans-serif;",
  subtitle: "color: #C084FC; font-size: 16px; font-weight: bold;", // Style baru untuk sapaan kedua
  text: "color: #8B5CF6; font-size: 14px;",
  error:
    "color: #EC4899; font-size: 16px; font-weight: bold; border: 1px dashed #EC4899; padding: 5px; border-radius: 5px;",
};

export const initConsoleFeatures = () => {
  // Bersihkan console
  try {
    console.clear();
  } catch (e) {}

  // Sapaan awal
  // 1. Judul Besar (Pink)
  console.log(`%c✨ Selamat datang di portofolio Abdian 👋`, styles.title);

  // 2. Sapaan Developer (Ungu Muda/Lilac - Lebih kecil dari judul)
  console.log(`%cHalo developer, kamu menemukan console! 🕵️‍♂️`, styles.subtitle);

  // 3. Instruksi (Ungu - Text biasa)
  console.log(`%cCoba ketik 'pacar' untuk melihat pacar Abdian`, styles.text);

  // Fitur Pacar
  Object.defineProperty(window, "pacar", {
    get: () => {
      // Langsung muncul error 404 sesuai request
      console.log(`%cError 404: Pacar Not Found 💔`, styles.error);
      return ""; // Biar ga ada tulisan undefined
    },
    configurable: true,
  });
};
