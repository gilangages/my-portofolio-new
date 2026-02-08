export const getAllProjects = async () => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/projects`, {
    method: "GET",
    headers: {
      Accept: "application/json",
    },
  });
};

export const getSingleProject = async (id) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/projects${id}`, {
    method: "GET",
    headers: {
      Accept: "application/json",
    },
  });
};

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

export const adminUpdateProject = async (token, id, formData) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/projects${id}`, {
    method: "PUT",
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    },
    body: formData,
  });
};

export const adminDeleteProject = async (token, id) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/projects/${id}`, {
    method: "DELETE",
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    },
  });
};
