export function handleValidateName(value) {
	if (!value) {
        return 'Name is required';
    }
    return '';
}

export function handleValidateUsername(value) {
	if (!value) {
        return 'Username is required';
    }
    return '';
}

export function handleValidateEmail(value) {
	if (!value) {
        return 'Email is required';
    }
    return '';
}

export function handleValidatePassword(value) {
	if (!value) {
        return 'Password is required';
    }
    return '';
}

export function handleValidatePhoneNumber(value) {
	if (!value) {
		return 'Phone number is required';
	}
	return '';
}

export function handleValidateUserRole(value) {
	if (!value) {
		return 'User role is required';
	}
	return '';
}

export function handleValidateImage(file) {
	if (!file) return ''; // it's nullable

	const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
	const maxSizeInKB = 4096;

	if (!allowedTypes.includes(file.type)) {
		return 'Invalid image format. Allowed: JPEG, PNG, JPG.';
	}

	const sizeInKB = file.size / 1024;
	if (sizeInKB > maxSizeInKB) {
		return 'Image must be less than 4MB.';
	}

	return '';
}

export function handleValidateMenuVariety(file) {
	if (!file) return '';

	const allowedTypes = [
		'application/pdf',
		'application/msword', // .doc
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
		'application/vnd.ms-excel', // .xls
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' // .xlsx
	];
	const maxSizeInKB = 4096;

	if (!allowedTypes.includes(file.type)) {
		return 'Invalid file format. Allowed: PDF, DOC, DOCX, XLS, XLSX.';
	}

	const sizeInKB = file.size / 1024;
	if (sizeInKB > maxSizeInKB) {
		return 'File must be less than 4MB.';
	}

	return '';
}

export function handleValidateMenu(file) {
	if (!file) return '';

	const isPdf = file.type === 'application/pdf';
	const maxSizeInKB = 4096;

	if (!isPdf) {
		return 'Invalid file format. Allowed: PDF only.';
	}

	if (file.size / 1024 > maxSizeInKB) {
		return 'File must be less than 4MB.';
	}

	return '';
}

export function handleValidateMasterName(value) {
	if (!value) {
		return 'Master name is required';
	}
	return '';
}

export function handleValidateMasterValue(value) {
	if (!value) {
		return 'Master value is required';
	}
	return '';
}

export function handleValidateEventName(value) {
	if (!value) {
		return 'Event name is required';
	}
	return '';
}

export function handleValidateContactPerson(value) {
	if (!value) {
		return 'Contact person is required';
	}
	return '';
}

export function handleValidateLocation(value) {
	if (!value) {
		return 'Location is required';
	}
	return '';
}

export function handleValidateFoodType(value) {
	if (!value) {
		return 'Food type is required';
	}
	return '';
}

export function handleValidateEventStatus(value) {
	if (!value) {
		return 'Event status is required';
	}
	return '';
}


export function handleValidateCostPerHead(value) {
	if (!value) {
		return 'cost per person is required';
	}
	return '';
}


export function handleValidateDescription(value) {
	if (!value) {
		return 'Description is required';
	}
	return '';
}

export function handleValidateMealType(value) {
	if (!value) {
		return 'Meal Type is required';
	}
	return '';
}

export function handleValidateStartDate(value) {
	if (!value) {
		return 'Start Date is required';
	}
	return '';
}

export function handleValidateStartTime(value) {
	if (!value) {
		return 'Start Time is required';
	}
	return '';
}

export function handleValidateEndDate(value) {
	if (!value) {
		return 'End Date is required';
	}
	return '';
}

export function handleValidateEndTime(value) {
	if (!value) {
		return 'End Time is required';
	}
	return '';
}

export function handleValidateVenue(value) {
	if (!value) {
		return 'Venue is required';
	}
	return '';
}

export function handleValidatePax(value) {
	if (!value) {
		return 'PAX is required';
	}
	if (isNaN(value) || Number(value) <= 0) {
		return 'PAX must be a valid number greater than 0';
	}
	return '';
}

export function handleValidateNote(value) {
	if (!value) {
		return 'Note is required';
	}
	return '';
}

export function handleValidateTaskType(value) {
	if (!value) {
		return 'Task Type is required';
	}
	return '';
}

export function handleValidateTaskStatus(value) {
	if (!value) {
		return 'Task Status is required';
	}
	return '';
}

export function handleValidateEventId(value) {
	if (!value) {
		return 'Event is required';
	}
	return '';
}

export function handleValidateUserIds(value) {
	if (!Array.isArray(value) || value.length === 0) {
        return 'At least one user is required';
    }
	return '';
}

export function handleValidateCategory(value) {
	if (!value) {
		return 'Category is required';
	}
	return '';
}

export function handleValidateBeforeDays(value) {
	if (value === null || value === undefined || value === '') {
		return 'Before days is required';
	}
	return '';
}

export function handleValidateBeforeHours(value) {
	if (value === null || value === undefined || value === '') {
		return 'Before hours is required';
	}
	return '';
}