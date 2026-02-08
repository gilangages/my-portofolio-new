import Swal from "sweetalert2";

export const alertSuccess = async (message) => {
  return Swal.fire({
    icon: "success",
    title: "Success",
    text: message,
  });
};

export const alertError = async (message) => {
  return Swal.fire({
    icon: "error",
    title: "Ups",
    text: message,
  });
};

export const alertConfirm = async (message) => {
  const result = await Swal.fire({
    title: "Hapus Skill?",
    text: message,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#000",
    cancelButtonColor: "#d33",
    confirmButtonText: "Ya, Hapus!",
  });
  return result.isConfirmed;
};

export const alertConfirmProject = async (message) => {
  const result = await Swal.fire({
    title: "Hapus Project?",
    text: message,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#000",
    cancelButtonColor: "#d33",
    confirmButtonText: "Ya, Hapus!",
  });
  return result.isConfirmed;
};

export const alertConfirmContact = async (message) => {
  const result = await Swal.fire({
    title: "Hapus Contact?",
    text: message,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#000",
    cancelButtonColor: "#d33",
    confirmButtonText: "Ya, Hapus!",
  });
  return result.isConfirmed;
};
