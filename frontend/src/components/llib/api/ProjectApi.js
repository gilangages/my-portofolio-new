export const adminUploadProject = async (token, formData) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/projects`, {
    method: "POST",
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    },
    body: formData,
  });
};
