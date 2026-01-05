# terraform/modules/docker-scenario/variables.tf

variable "student_id" {
  description = "Unique student identifier"
  type        = string
}

variable "vlan_id" {
  description = "VLAN ID for network isolation"
  type        = number
  validation {
    condition     = var.vlan_id >= 101 && var.vlan_id <= 200
    error_message = "VLAN ID must be between 101 and 200."
  }
}

variable "scenario_name" {
  description = "Name of the scenario (sql-injection, xss, etc.)"
  type        = string
}

variable "scenario_path" {
  description = "Path to scenario Dockerfile and files"
  type        = string
}

variable "session_duration" {
  description = "Session duration in hours"
  type        = number
  default     = 4
}

# NEW: Control database container creation
variable "use_separate_db" {
  description = "Whether to create a separate database container"
  type        = bool
  default     = true
}

# NEW: Control Ansible execution
variable "run_ansible" {
  description = "Whether to run Ansible post-configuration"
  type        = bool
  default     = true
}
