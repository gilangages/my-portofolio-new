export const getAllServices = async (showAll = false) => {
  const url = showAll
    ? `${import.meta.env.VITE_APP_PATH}/services?all=true`
    : `${import.meta.env.VITE_APP_PATH}/services`;

  return await fetch(url, {
    method: "GET",
    headers: {
      Accept: "application/json",
    },
  });
};

export const getSingleService = async (id) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/services/${id}`, {
    method: "GET",
    headers: {
      Accept: "application/json",
    },
  });
};

export const adminUploadService = async (token, payload) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/services`, {
    method: "POST",
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
      Accept: "application/json",
    },
    body: JSON.stringify(payload),
  });
};

export const adminUpdateService = async (token, id, payload) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/services/${id}`, {
    method: "PUT",
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
      Accept: "application/json",
    },
    body: JSON.stringify(payload),
  });
};

export const adminDeleteService = async (token, id) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/services/${id}`, {
    method: "DELETE",
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    },
  });
};
