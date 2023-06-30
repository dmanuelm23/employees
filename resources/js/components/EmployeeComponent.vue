<template>
    <div v-if="messageAlert">
        <Notification :message="messageAlert" @statusAlertMsg="cambiarMsgAlert" />
    </div>
    <div class="container mt-4">
        <h4>Lista de empleados</h4>
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-striped dt-responsive nowrap" id="employees">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Puesto</th>
                            <th>Fecha de nacimiento</th>
                            <th>Domicilio</th>
                            <th>Habilidades</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Puesto</th>
                            <th>Fecha de nacimiento</th>
                            <th>Habilidades</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <tr v-for="employee in employees" :key="employee.id">
                            <td>{{ employee.id }}</td>
                            <td>{{ employee.name }}</td>
                            <td>{{ employee.email }}</td>
                            <td>{{ employee.position }}</td>
                            <td>{{ employee.birthdate }}</td>
                            <td>{{ employee.address }}</td>
                            <td><span v-for="(skill, index) in employee.skills" :key="index">{{ skill.skill}}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_add_employee" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmployeeModalLabel">{{titleModalEmployee}} Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="reset()"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <label for="name">Nombre: <span class="text-danger small">*</span></label>
                            <input @click="validateInput(1)" class="form-control" type="text" name="name" id="name" v-model="dataEmployee.name"/>
                            <small style="color:#FF6868;" v-for="(e_name, index) in errors.name" :key="index">{{e_name}}</small>
                        </div>
                        <div class="col-sm-6">
                            <label for="email">Correo electrónico: <span class="text-danger small">*</span></label>
                            <input @click="validateInput(2)" class="form-control" type="email" name="email" id="email" v-model="dataEmployee.email"/>
                            <small style="color:#FF6868;" v-for="(e_email, index) in errors.email" :key="index">{{e_email}}</small>
                        </div>
                        <div class="col-sm-6">
                            <label for="position">puesto: <span class="text-danger small">*</span></label>
                            <input @click="validateInput(3)" class="form-control" type="text" name="position" id="position" v-model="dataEmployee.position"/>
                            <small style="color:#FF6868;" v-for="(e_position, index) in errors.position" :key="index">{{e_position}}</small>
                        </div>
                        <div class="col-sm-6">
                            <label for="birthdate">Fecha de cumpleaños: <span class="text-danger small">*</span></label>
                            <input @click="validateInput(4)" class="form-control" type="date" name="birthdate" id="birthdate" v-model="dataEmployee.birthdate"/>
                            <small style="color:#FF6868;" v-for="(e_birthdate, index) in errors.birthdate" :key="index">{{e_birthdate}}</small>
                        </div>
                        <div class="col-sm-6">
                            <label for="address">Dirección: <span class="text-danger small">*</span></label>
                            <input @click="validateInput(5)" class="form-control" type="text" name="address" id="address" v-model="dataEmployee.address"/>
                            <small style="color:#FF6868;" v-for="(e_address, index) in errors.address" :key="index">{{e_address}}</small>
                        </div>

                        <div class="col-sm-6">
                            <label for="skills">Habilidades: <span class="text-danger small">*</span></label>
                            <textarea  class="form-control" @click="validateInput(6)" name="skills" id="skills" cols="30" rows="10"  v-model="dataEmployee.skills"></textarea>
                            <small style="color:#FF6868;" v-for="(e_skills, index) in errors.skills" :key="index">{{e_skills}}</small>
                            <span>Agrega una o mas habilidades separadas por una coma</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <p class="small">
                            <span class="text-danger small">*</span> Datos requeridos
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="reset()" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button @click="save()" type="button" class="btn btn-primary" v-text="option=='create'?'Guardar':'Guardar'"></button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
//Bootstrap and jQuery libraries
import "bootstrap/dist/css/bootstrap.min.css";
import "jquery/dist/jquery.min.js";
//Datatable Modules
import "datatables.net-dt/js/dataTables.dataTables";
import "datatables.net-dt/css/jquery.dataTables.min.css";
import "datatables.net-buttons/js/dataTables.buttons.js";
import "datatables.net-buttons/js/buttons.colVis.js";
import "datatables.net-buttons/js/buttons.flash.js";
import "datatables.net-buttons/js/buttons.html5.js";
import "datatables.net-buttons/js/buttons.print.js";
import JSZip from "jszip";
import pdfMake from "pdfmake/build/pdfmake";
import pdfFonts from "pdfmake/build/vfs_fonts";
pdfMake.vfs = pdfFonts.pdfMake.vfs;
window.JSZip = JSZip;
import $ from "jquery";
import axios from "axios";
import Notification from "./Notification";
export default {
    inject: ["datatablesConfigUser"],
    components: {
        Notification,
    },
    data() {
        return {
            titleModalEmployee:"",
            titleStatus:"",
            employees:[],
            errors:[],
            dataEmployee: {
                id: null,
                name: null,
                email: null,
                birthdate: null,
                position: null,
                address: null,
                skills: null,
                
            },
            employeeModal: "",
            option: "",
            employee_id: "",
            employee_name: "",
            employee_status:1,
            dataTable: null,
            messageAlert:0,
        };
    },
    methods: {
        cambiarMsgAlert(value){
            this.messageAlert= value;
        },
        getList(){
            axios.get('/api/listemployees').then(response=>{
                this.employees = response.data.employees;
            })
        },
        validateInput(value) {
            switch (value) {
                case 1:
                    this.errors.name = "";
                break;
                case 2:
                    this.errors.email = "";
                    break;
                case 3:
                    this.errors.position = "";
                    break;
                case 4:
                    this.errors.birthdate = "";
                    break;
                case 5:
                    this.errors.address = "";
                    break;
                case 6:
                  this.errors.skills = "";
                  break;
            }
        },
        openModalAdd() {
            this.reset();
            this.option = "create";
            this.titleModalEmployee = "Agregar";
            this.employeeModal.show();
        },
        save() {
            if (this.option == "create"){
                axios.post('/api/employees', this.dataEmployee).then(response => {
                    if(response && response.data.statusCode == 200){
                        this.messageAlert = response.data.message;
                        this.reset();
                         this.employeeModal.hide();
                        this.getList();
                    }
                    else{
                        console.log(response);
                    }
                }).catch((error)=> {
                    if (error.response.status == 422){
                        this.errors = error.response.data.errors;
                    }else{
                        console.log(this.errors);
                    }
                });
            }
        },
        reset() {
            this.employee_id = "";
            this.employee_name = "";
            this.dataEmployee.id = null;
            this.dataEmployee.name = null;
            this.dataEmployee.email = null;
            this.dataEmployee.position = null;
            this.dataEmployee.address = null;
            this.dataEmployee.birthdate = null;
            this.dataEmployee.skills = null;
            this.titleModalEmployee="";
        },
  },
  mounted() {
    this.getList();
    this.employeeModal = new bootstrap.Modal(
      document.getElementById("modal_add_employee")
    );
    var tooltipTriggerList = [].slice.call(
      document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  },
  created() {
    let self = this;
    this.$nextTick(() => {
      self.dataTable = $("#employees").DataTable(self.datatablesConfigUser);
      new $.fn.dataTable.Buttons(self.dataTable, {
        name: "otherButton",
        buttons: [
          {
            className: "btn btn-secondary btn-sm border",
            text: '<i class="ri-user-add-line"></i>',
            titleAttr: "Agregar",
            exportOptions: {
              columns: ":visible",
            },
            action: function (e, dt, node, config) {
              self.openModalAdd(e.target.dataset.itemId);
            },
          },
        ],
      });
      self.dataTable
        .buttons("otherButton", null)
        .containers()
        .addClass("groupleft")
        .insertBefore(".dataTables_filter");
    });
  },
  watch: {
    errors: function (newVal) {
      if (Object.values(newVal).length == 0) {
        this.employeeModal.hide();
        this.reset();
      }
    },
    employees() {
      let self = this;
      if (self.dataTable) {
        self.dataTable.destroy();
      }
      this.$nextTick(() => {
        self.dataTable = $("#employees").DataTable(self.datatablesConfigUser);
        new $.fn.dataTable.Buttons(self.dataTable, {
          name: "otherButton",
          buttons: [
            {
              className: "btn btn-secondary btn-sm border",
              text: '<i class="ri-user-add-line"></i>',
              titleAttr: "Agregar",
              exportOptions: {
                columns: ":visible",
              },
              action: function (e, dt, node, config) {
                self.openModalAdd(e.target.dataset.itemId);
              },
            },
          ],
        });
        self.dataTable
          .buttons("otherButton", null)
          .containers()
          .addClass("groupright")
          .insertBefore(".dataTables_filter");
      });
    },
  },
};
</script>
<style scoped>
  .small {
    font-size: smaller;
  }
  .active_password{
      font-size: smaller;
      color: #33FFA9;
  }
  .desactive_password{
    font-size: smaller;
    color: #C4C4C4;
    transition: all 0.3s;
  }
</style>

