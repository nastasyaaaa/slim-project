pipeline {
    agent any
    options {
        timestamps()
    }
    environment {
        CI = 'true'
        DOCKER_BUILD = 1
        REGISTRY = credentials('REGISTRY')
        IMAGE_TAG = sh(returnStdout: true, script: "echo '${env.BUILD_TAG}' | sed 's/%2F/-/g'").trim()
    }
    stages {
        stage("Init") {
            steps {
                sh "make init"
            }
        }
        stage("Validate schema") {
            steps {
                sh "make validate-schema"
            }
        }
//         stage("Lint") {
//             parallel {
//                 stage("API") {
//                     steps {
//                         sh "sleep 1"
//                     }
//                 }
//                 stage("FrontEnd") {
//                     steps {
//                         sh "sleep 1"
//                     }
//                 }
//             }
//         }
//         stage("Analyze") {
//             steps {
//                 sh "sleep 1"
//             }
//         }
        stage("Test") {
            parallel {
                stage("API") {
                    steps {
                        sh "make api-test"
                    }
                }
//                 stage("FrontEnd") {
//                     steps {
//                         sh "sleep 1"
//                     }
//                 }
            }
        }
        stage("Down") {
            steps {
                sh "make down"
            }
        }
        stage("Build Production Images") {
            steps {
                sh "make build"
            }
        }
       stage("Testing") {
           stages {
               stage("Build Testing Images") {
                    steps {
                        sh "make testing-build"
                    }
               }
               stage("Init") {
                    steps {
                        sh "make testing-init"
                    }
               }
//                 stage("Smoke") {
//                     steps {
//                         sh "sleep 1"
//                     }
//                 }
//                 stage("E2E") {
//                     steps {
//                         sh "sleep 1"
//                     }
//                 }
                stage("Down") {
                    steps {
                        sh "make testing-down-clear"
                    }
                }
           }
       }
       stage("Push Production Images") {
            when {
                branch 'master'
            }
            steps {
                withCredentials([
                    usernamePassword(
                        credentialsId: 'REGISTRY_AUTH',
                        usernameVariable: 'USER',
                        passwordVariable: 'PASSWORD'
                    )
                ]) {
                    sh "docker login -u='$USER' -p='$PASSWORD' $REGISTRY"
                }

                sh "make push"
            }
       }
    }
    post {
        always {
            sh "make down || true"
            sh "make testing-down-clear || true"
        }
    }
}